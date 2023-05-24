const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('users', {
    id: {
      autoIncrement: true,
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    email: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    code: {
      type: DataTypes.STRING(15),
      allowNull: false
    },
    name: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    gender: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    picture: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    status: {
      type: DataTypes.STRING(2),
      allowNull: false
    },
    created_by: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false
    },
    updated_by: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false
    },
  }, {
    sequelize,
    tableName: 'users',
    timestamps: true,
    underscored: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
      {
        name: "users_created_by_index",
        using: "BTREE",
        fields: [
          { name: "created_by" },
        ]
      },
      {
        name: "users_updated_by_index",
        using: "BTREE",
        fields: [
          { name: "updated_by" },
        ]
      },
    ]
  });
};
