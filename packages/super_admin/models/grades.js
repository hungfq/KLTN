const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('grades', {
    id: {
      autoIncrement: true,
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    topic_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false,
      references: {
        model: 'topics',
        key: 'id'
      }
    },
    criteria_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false,
      references: {
        model: 'criteria',
        key: 'id'
      }
    },
    student_id: {
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false,
      references: {
        model: 'users',
        key: 'id'
      }
    },
    score: {
      type: DataTypes.DOUBLE(8,2),
      allowNull: false
    },
    graded_by: {
      type: DataTypes.BIGINT.UNSIGNED,
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
    deleted: {
      type: DataTypes.TINYINT,
      allowNull: false,
      defaultValue: 0
    }
  }, {
    sequelize,
    tableName: 'grades',
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
        name: "grades_topic_id_index",
        using: "BTREE",
        fields: [
          { name: "topic_id" },
        ]
      },
      {
        name: "grades_criteria_id_index",
        using: "BTREE",
        fields: [
          { name: "criteria_id" },
        ]
      },
      {
        name: "grades_student_id_index",
        using: "BTREE",
        fields: [
          { name: "student_id" },
        ]
      },
      {
        name: "grades_graded_by_index",
        using: "BTREE",
        fields: [
          { name: "graded_by" },
        ]
      },
      {
        name: "grades_created_by_index",
        using: "BTREE",
        fields: [
          { name: "created_by" },
        ]
      },
      {
        name: "grades_updated_by_index",
        using: "BTREE",
        fields: [
          { name: "updated_by" },
        ]
      },
    ]
  });
};
