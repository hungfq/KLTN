const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('topics', {
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
    thesis_defense_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    schedule_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: true,
      references: {
        model: 'schedules',
        key: 'id'
      }
    },
    committee_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: true,
      references: {
        model: 'committees',
        key: 'id'
      }
    },
    committee_ordinal: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    lecturer_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: true
    },
    critical_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: true
    },
    lecturer_approved: {
      type: DataTypes.BOOLEAN,
      allowNull: true
    },
    critical_approved: {
      type: DataTypes.BOOLEAN,
      allowNull: true
    },
    lecturer_grade: {
      type: DataTypes.DOUBLE(8,2),
      allowNull: true
    },
    critical_grade: {
      type: DataTypes.DOUBLE(8,2),
      allowNull: true
    },
    committee_president_grade: {
      type: DataTypes.DOUBLE(8,2),
      allowNull: true
    },
    committee_secretary_grade: {
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
    tableName: 'topics',
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
        name: "topics_schedule_id_foreign",
        using: "BTREE",
        fields: [
          { name: "schedule_id" },
        ]
      },
      {
        name: "topics_committee_id_foreign",
        using: "BTREE",
        fields: [
          { name: "committee_id" },
        ]
      },
      {
        name: "topics_created_by_index",
        using: "BTREE",
        fields: [
          { name: "created_by" },
        ]
      },
      {
        name: "topics_updated_by_index",
        using: "BTREE",
        fields: [
          { name: "updated_by" },
        ]
      },
    ]
  });
};
