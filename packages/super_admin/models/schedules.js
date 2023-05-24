const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('schedules', {
    id: {
      autoIncrement: true,
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    code: {
      type: DataTypes.STRING(15),
      allowNull: false
    },
    name: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    description: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    start_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    end_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    proposal_start: {
      type: DataTypes.DATE,
      allowNull: true
    },
    proposal_end: {
      type: DataTypes.DATE,
      allowNull: true
    },
    approve_start: {
      type: DataTypes.DATE,
      allowNull: true
    },
    approve_end: {
      type: DataTypes.DATE,
      allowNull: true
    },
    register_start: {
      type: DataTypes.DATE,
      allowNull: true
    },
    register_end: {
      type: DataTypes.DATE,
      allowNull: true
    },
    advisor_score_rate: {
      type: DataTypes.DOUBLE(8,2),
      allowNull: true
    },
    critical_score_rate: {
      type: DataTypes.DOUBLE(8,2),
      allowNull: true
    },
    president_score_rate: {
      type: DataTypes.DOUBLE(8,2),
      allowNull: true
    },
    secretary_score_rate: {
      type: DataTypes.DOUBLE(8,2),
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
    tableName: 'schedules',
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
        name: "schedules_created_by_index",
        using: "BTREE",
        fields: [
          { name: "created_by" },
        ]
      },
      {
        name: "schedules_updated_by_index",
        using: "BTREE",
        fields: [
          { name: "updated_by" },
        ]
      },
    ]
  });
};
