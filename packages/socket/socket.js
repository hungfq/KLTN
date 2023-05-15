const server = require('http').Server();
const { Server } = require('socket.io');
const { getRedis } = require('./redis');

const PORT = 8002;
server.listen(PORT);

const socket = new Server(server, {
  cors: {
    origin: [
      'http://localhost:8080',
      'https://localhost:8001',
      'https://localhost:80',
      'http://localhost:80',
    ],
    methods: ['GET', 'POST', 'OPTIONS'],
  },
});

console.log(`Socket server is running ... at ${PORT}`);

const getSocketIdByUserId = async (userId) => {
  const redis = await getRedis();

  const socketId = await redis.get(userId.toString());

  return socketId;
};

socket.on('connection', async (io) => {
  console.log(`socket: ${io.id} [CONNECTING.....]`);

  io.on('login', async (id) => {
    if (id !== null) {
      const redis = await getRedis();

      await redis.set(id.toString(), io.id);
    }
    console.log(`socket: ${io.id} ; client: ${id}`);
  });

  io.on('disconnect', () => {
    // TO DO: remove socket_id of user
    console.log(`socket: ${io.id} [DISCONNECTED]`);
  });

  io.on('test', () => {
    // TO DO: remove socket_id of user
    console.log(`socket: ${io.id} [TEST]`);
  });

  io.on('update-task', async (data) => {
    if (data.id) {
      const socketId = await getSocketIdByUserId(data.id);

      await socket.to(socketId).emit('task', data.topicId);
    }
  });

  io.on('update-notify', async (data) => {
    if (data.id) {
      const socketId = await getSocketIdByUserId(data.id);

      await socket.to(socketId).emit('notify', data.notification);
    }
  });
});
