/*
 Navicat MySQL Data Transfer

 Source Server         : venezia
 Source Server Type    : MySQL
 Source Server Version : 50721
 Source Host           : localhost:3306
 Source Schema         : sahand

 Target Server Type    : MySQL
 Target Server Version : 50721
 File Encoding         : 65001

 Date: 06/04/2019 20:37:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for use_access_data_mock
-- ----------------------------
DROP TABLE IF EXISTS `use_access_data_mock`;
CREATE TABLE `use_access_data_mock`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `roles` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `permissions` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `denialPermissions` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `FK_use_access_data_mock_users`(`user_id`) USING BTREE,
  CONSTRAINT `FK_use_access_data_mock_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of use_access_data_mock
-- ----------------------------
INSERT INTO `use_access_data_mock` VALUES (1, 1, '1', NULL, NULL, '2018-09-24 10:11:26', '2018-09-26 09:33:36', NULL);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `person_id` int(10) UNSIGNED NULL DEFAULT NULL,
  `active` tinyint(4) NULL DEFAULT NULL,
  `email` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `mobile` char(24) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `last_login` timestamp(0) NULL DEFAULT NULL,
  `last_login_ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `create_user_id` int(11) NULL DEFAULT NULL,
  `update_user_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 1, 1, 'm@m.m', '09353774850', '$2y$10$0AlVqRJ/v8503Ye6EJCtH.v5HD3yrUrJDwRDsQHtcwhphalXTCeuK', NULL, NULL, NULL, NULL, 'u9TV3eMC3IE3DMXiQF9b1bZtbFYU2LsBqRvhLLCsivUhcM8b7lc9oQxmJGCr', NULL, 1, '2018-05-13 10:52:16', '2019-03-01 14:18:51', NULL);
INSERT INTO `users` VALUES (2, 2, 1, 'mahdi.shakki@gmail.com', '09301112222', '$2y$10$kCHBhX/XaaSye.rYdhryQ.URUJP.Eu1qUFdjfNvwXlJy6HvxzZenG', NULL, NULL, NULL, 'u2_1551403074_5437.jpg', NULL, 1, 1, '2019-03-01 01:17:54', '2019-03-25 10:44:09', '2019-03-25 10:44:09');
INSERT INTO `users` VALUES (3, 3, 1, NULL, '09112225555', '$2y$10$08yQZ2CICNHTS46/Fqb57OUwjjvbYyACDwU4bQ7nrnIbmVLlBse5C', NULL, NULL, NULL, 'u3_1551403376_3869.jpg', NULL, 1, NULL, '2019-03-01 01:22:56', '2019-03-01 12:50:03', '2019-03-01 12:50:03');
INSERT INTO `users` VALUES (4, 4, 1, NULL, '09114445555', '$2y$10$P9TC3jV1oRxExi0hRRw1lOcYePgew86eGp8XBb3j/F6L0/pG1LFuK', NULL, NULL, NULL, NULL, NULL, 1, NULL, '2019-03-01 01:24:49', '2019-03-01 12:49:13', '2019-03-01 12:49:13');

-- ----------------------------
-- Table structure for usr_model_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `usr_model_has_permissions`;
CREATE TABLE `usr_model_has_permissions`  (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `model_id`, `model_type`) USING BTREE,
  INDEX `model_has_permissions_model_id_model_type_index`(`model_id`, `model_type`) USING BTREE,
  CONSTRAINT `usr_model_has_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `usr_permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for usr_model_has_roles
-- ----------------------------
DROP TABLE IF EXISTS `usr_model_has_roles`;
CREATE TABLE `usr_model_has_roles`  (
  `role_id` int(11) NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `model_id`, `model_type`) USING BTREE,
  INDEX `model_has_roles_model_id_model_type_index`(`model_id`, `model_type`) USING BTREE,
  CONSTRAINT `FK_usr_model_has_roles_usr_roles` FOREIGN KEY (`role_id`) REFERENCES `usr_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of usr_model_has_roles
-- ----------------------------
INSERT INTO `usr_model_has_roles` VALUES (1, 'App\\User', 1);

-- ----------------------------
-- Table structure for usr_permissions
-- ----------------------------
DROP TABLE IF EXISTS `usr_permissions`;
CREATE TABLE `usr_permissions`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent` int(11) NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `type` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'GET',
  `f_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `action` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `create_user_id` int(11) NULL DEFAULT NULL,
  `update_user_id` int(11) NULL DEFAULT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of usr_permissions
-- ----------------------------
INSERT INTO `usr_permissions` VALUES (1, 0, 1, 'GET', 'داشبورد', 'Dashboard', 'Admin\\AdminController@dashboard', '/dashboard', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (2, 0, 1, 'GET', 'کاربران', 'Users list', 'Admin\\Users\\UsersController@users', 'users/users', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (3, 2, 1, 'POST', 'کاربران - دریافت دیتا', 'Users list - data table', 'Admin\\Users\\UsersController@anyData', 'users/dataTables', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (4, 2, 1, 'POST', 'کاربران - ایجاد کاربر', 'Users list - create user', 'Admin\\Users\\UsersController@userCreate', 'users/userCreate', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (5, 2, 1, 'POST', 'کاربران - به روز رسانی کاربر', 'Users list - update user', 'Admin\\Users\\UsersController@userUpdate', 'users/userUpdate', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (6, 2, 1, 'POST', 'کاربران - حذف کاربر', 'Users list - delete user', 'Admin\\Users\\UsersController@userDelete', 'users/delete', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (7, 2, 1, 'POST', 'کاربران - دریافت اطلاعات کاربر با آی دی', 'Users list - get user info by id', 'Admin\\Users\\UsersController@getUserInfoById', 'users/getUserInfoById', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (8, 2, 1, 'POST', 'کاربران - آپلود نمایه', 'Users list - upload avatar', 'Admin\\Users\\UsersController@uploadUserAvatar', 'users/uploadUserAvatar', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (9, 0, 1, 'GET', 'نقش های کاربری', 'Roles', 'Admin\\Users\\RolePermissionController@group', 'users/group', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (10, 9, 1, 'POST', 'نقش های کاربری - دریافت دیتا', 'Roles - data table', 'Admin\\Users\\RolePermissionController@rolesAnyData', 'users/group/dataTables', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (11, 9, 1, 'POST', 'نقش های کاربری - ایجاد نقش', 'Roles - create role', 'Admin\\Users\\RolePermissionController@createRole', 'users/group/create', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (12, 9, 1, 'POST', 'نقش های کاربری - بروز رسانی نقش', 'Roles - update role', 'Admin\\Users\\RolePermissionController@updateRole', 'users/group/update', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (13, 9, 1, 'POST', 'نقش های کاربری - حذف نقش', 'Roles - delete role', 'Admin\\Users\\RolePermissionController@deleteRole', 'users/group/delete', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (14, 9, 1, 'POST', 'نقش های کاربری - دریافت اطلاعات نقش', 'Roles - get role info', 'Admin\\Users\\RolePermissionController@getRoleInfo', 'users/group/getRoleInfo', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (15, 0, 1, 'GET', 'دسترسی ها', 'Permissions', 'Admin\\Users\\RolePermissionController@permissionIndex', 'users/permission', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (16, 15, 1, 'POST', 'دسترسی ها - دریافت دیتا', 'Permissions - data table', 'Admin\\Users\\RolePermissionController@permissionAnyData', 'users/permission/dataTables', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (17, 15, 1, 'POST', 'دسترسی ها - ذخیره', 'Permissions - create permission', 'Admin\\Users\\RolePermissionController@permissionSave', 'users/permission/permissionSave', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (18, 15, 1, 'POST', 'دسترسی ها - دریافت اطلاعات', 'Permissions - get information by id', 'Admin\\Users\\RolePermissionController@getUserRolePerInfo', 'users/permission/getUserRolePerInfo', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');
INSERT INTO `usr_permissions` VALUES (19, 15, 1, 'POST', 'دسترسی ها - دریافت اطلاعات جدید', 'Permissions - get updated info by id', 'Admin\\Users\\RolePermissionController@getNewPermissionOnRoleChange', 'users/permission/getNewPermissionOnRoleChange', NULL, NULL, NULL, 'web', '2018-09-05 15:53:15', '2018-09-05 15:53:17');

-- ----------------------------
-- Table structure for usr_persons
-- ----------------------------
DROP TABLE IF EXISTS `usr_persons`;
CREATE TABLE `usr_persons`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `family` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `gender_id` tinyint(1) NULL DEFAULT NULL COMMENT '0 is femail and 1 is male',
  `national_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `dob` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of usr_persons
-- ----------------------------
INSERT INTO `usr_persons` VALUES (1, 1, 'مهدی', 'شکی', 1, '0014202077', NULL, NULL, NULL, '2018-09-02 16:30:44', '2019-03-01 14:18:51', NULL);
INSERT INTO `usr_persons` VALUES (2, 1, 'mehdi', 'shakki', 0, '2260083617', NULL, 'tehran', NULL, '2019-03-01 01:17:54', '2019-03-25 10:44:09', '2019-03-25 10:44:09');
INSERT INTO `usr_persons` VALUES (3, 1, 'sf', 'df', 1, '2260083617', '', NULL, NULL, '2019-03-01 01:22:56', '2019-03-01 12:50:03', '2019-03-01 12:50:03');
INSERT INTO `usr_persons` VALUES (4, 1, 'cvxc', 'xcvx', 1, '', '', NULL, NULL, '2019-03-01 01:24:49', '2019-03-01 12:49:13', '2019-03-01 12:49:13');

-- ----------------------------
-- Table structure for usr_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `usr_role_permissions`;
CREATE TABLE `usr_role_permissions`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `create_user_id` int(11) NULL DEFAULT NULL,
  `update_user_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `FK_usr_role_permissions_usr_permissions`(`permission_id`) USING BTREE,
  INDEX `FK_usr_role_permissions_usr_roles`(`role_id`) USING BTREE,
  CONSTRAINT `FK_usr_role_permissions_usr_permissions` FOREIGN KEY (`permission_id`) REFERENCES `usr_permissions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_usr_role_permissions_usr_roles` FOREIGN KEY (`role_id`) REFERENCES `usr_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of usr_role_permissions
-- ----------------------------
INSERT INTO `usr_role_permissions` VALUES (1, 1, 1, 1, NULL, NULL, NULL, '2019-02-22 16:23:29', '2019-02-22 16:23:29');
INSERT INTO `usr_role_permissions` VALUES (2, 1, 2, 1, NULL, NULL, NULL, '2019-02-22 16:23:44', '2019-02-22 16:23:44');
INSERT INTO `usr_role_permissions` VALUES (3, 1, 3, 1, NULL, NULL, NULL, '2019-02-22 16:23:44', '2019-02-22 16:23:44');
INSERT INTO `usr_role_permissions` VALUES (4, 1, 4, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (5, 1, 5, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (6, 1, 8, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (7, 1, 6, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (8, 1, 7, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (9, 1, 9, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (10, 1, 10, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (11, 1, 11, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (12, 1, 12, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (13, 1, 13, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (14, 1, 14, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (15, 1, 15, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (16, 1, 16, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (17, 1, 17, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (18, 1, 18, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');
INSERT INTO `usr_role_permissions` VALUES (19, 1, 19, 1, NULL, NULL, NULL, '2019-03-01 02:31:38', '2019-03-01 02:31:38');

-- ----------------------------
-- Table structure for usr_roles
-- ----------------------------
DROP TABLE IF EXISTS `usr_roles`;
CREATE TABLE `usr_roles`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sign` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `create_user_id` int(11) NULL DEFAULT NULL,
  `update_user_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of usr_roles
-- ----------------------------
INSERT INTO `usr_roles` VALUES (1, NULL, NULL, 'مدیر سیستم', 'web', 1, NULL, NULL, NULL, '2018-09-17 15:47:06', '2018-09-22 09:54:52');

SET FOREIGN_KEY_CHECKS = 1;
