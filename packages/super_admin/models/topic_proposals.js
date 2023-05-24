const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('topic_proposals', {
    id: {
      autoIncrement: true,
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    code: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    title: {
      type: DataTypes.STRING(200),
      allowNull: false
    },
    description: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    limit: {
      type: DataTypes.TINYINT,
      allowNull: false
    },
    schedule_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: true
    },
    lecturer_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: true
    },
    status: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    created_by: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false
    },
    updated_by: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false
    },
    deleted: {
      type: DataTypes.TINYINT,
      allowNull: false,
      defaultValue: 0
    }
  }, {
    sequelize,
    tableName: 'topic_proposals',
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
        name: "topic_proposals_created_by_index",
        using: "BTREE",
        fields: [
          { name: "created_by" },
        ]
      },
      {
        name: "topic_proposals_updated_by_index",
        using: "BTREE",
        fields: [
          { name: "updated_by" },
        ]
      },
    ]
  });
};
