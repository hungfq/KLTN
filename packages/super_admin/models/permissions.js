const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('permissions', {
    id: {
      autoIncrement: true,
      type: DataTypes.BIGINT.UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    name: {
      type: DataTypes.STRING(191),
      allowNull: true
    },
    action: {
      type: DataTypes.STRING(191),
      allowNull: false,
      unique: "permissions_action_unique"
    },
    description: {
      type: DataTypes.STRING(191),
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'permissions',
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
        name: "permissions_action_unique",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "action" },
        ]
      },
    ]
  });
};
