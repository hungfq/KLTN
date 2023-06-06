import "dotenv/config";
import { DataSource } from "typeorm";
import { OpenAI } from "langchain/llms/openai";
import { SqlDatabase } from "langchain/sql_db";
import {
  SqlDatabaseChain,
  VectorDBQAChain,
  ConversationChain
} from "langchain/chains";
import { createSqlAgent, SqlToolkit } from "langchain/agents";
import { Cohere } from "langchain/llms/cohere";
import { GoogleVertexAI } from "langchain/llms/googlevertexai";
import { HuggingFaceInference } from "langchain/llms/hf";
import { OpenAIEmbeddings } from "langchain/embeddings/openai";
import { TextLoader } from "langchain/document_loaders/fs/text";
import { VectorStoreRetrieverMemory, BufferMemory } from "langchain/memory";
import { MemoryVectorStore } from "langchain/vectorstores/memory";
import { HNSWLib } from "langchain/vectorstores/hnswlib";
import { SerpAPI, ChainTool } from "langchain/tools";

const a = async () => {
  const model = new OpenAI({
    temperature: 0,
    openAIApiKey: "",
    verbose: true
  });
  // const loader = new TextLoader("./tranningdata.txt");
  // const docs = await loader.load();
  // console.log("🚀 ~ file: index.js:19 ~ a ~ docs:", docs);
  // // load the docs into the vector
  const embeddings = new OpenAIEmbeddings({
    openAIApiKey: "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"
  });
  // // create the vector store
  // const vStore = await HNSWLib.fromDocuments(docs, embeddings);
  // console.log("🚀 ~ file: index.js:36 ~ a ~ vStore:", vStore);

  const vectorStore = new MemoryVectorStore(embeddings);
  const memory = new VectorStoreRetrieverMemory({
    // 1 is how many documents to return, you might want to return more, eg. 4
    vectorStoreRetriever: vectorStore.asRetriever(1),
    memoryKey: "history"
  });

  /* Create the chain */
  // const chain = VectorDBQAChain.fromLLM(model, vStore);

  // const memory = new BufferMemory();

  // First let's save some information to memory, as it would happen when
  // used inside a chain.
  await memory.saveContext(
    { input: "Quyền hạn là gì?" },
    { output: "quyền hạn là role" }
  );
  await memory.saveContext(
    { input: "STUDENT là role sinh viên" },
    { output: "STUDENT là role sinh viên" }
  );
  await memory.saveContext(
    { input: "LECTURER là role giáo viên hoặc còn được gọi là giảng viên" },
    { output: "LECTURER là role giáo viên hoặc còn được gọi là giảng viên" }
  );
  await memory.saveContext(
    { input: "grades là table chứa điểm bời đề tài, tiêu chí, sinh viên" },
    { output: "grades là table chứa điểm bời đề tài, tiêu chí, sinh viên" }
  );
  await memory.saveContext(
    { input: "grades là table chứa điểm bời đề tài, tiêu chí, sinh viên" },
    { output: "grades là table chứa điểm bời đề tài, tiêu chí, sinh viên" }
  );

  const chain = new ConversationChain({ llm: model, memory });
  const qaTool = new ChainTool({
    name: "db-table-description",
    description:
      "This tool will return the description of the table in the database",
    chain
  });
  // Working normally with a memory vector store
  const datasource = new DataSource({
    type: "mysql",
    database: "kltn",
    username: "root",
    password: "123456",
    connectionLimit: 10,
    port: 3306,
    host: "localhost",
    connectorPackage: "mysql2"
  });

  const db = await SqlDatabase.fromDataSourceParams({
    appDataSource: datasource,
    includeTables: ["Track"],
    sampleRowsInTableInfo: 0
  });
  // const info = db.allTables.forEach(table => console.log(table));
  // console.log(db.appDataSourceOptions);

  const chainSQL = new SqlDatabaseChain({
    llm: model,
    database: db,
    sqlOutputKey: "sql"
  });
  // chainSQL.memory = memory;
  // const res = await chainSQL.call("Get all users are there?");
  // const res = await chainSQL.call({ query: "Get all users are there?" });
  // console.log("🚀 ~ file: index.js:106 ~ a ~ res:", res);

  // console.log("🚀 ~ file: index.js:107 ~ a ~ res:", res);
  // console.log(chainSQL.memory);

  // const executor = await initializeAgentExecutorWithOptions(tools, model, {
  //   agentType: "structured-chat-zero-shot-react-description",
  //   verbose: true
  // });
  const toolkit = new SqlToolkit(db, model);
  const executor = createSqlAgent(model, toolkit);

  const input = `Danh sách đề tài có thời hạn chấm điểm trong tháng 6 ?`;
  const result = await executor.call({ input });
  console.log("🚀 ~ file: index.js:111 ~ a ~ result:", result);
};
a();
