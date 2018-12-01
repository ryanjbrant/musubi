# ************************************************************
# Sequel Pro SQL dump
# Version 4529
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.17)
# Database: common
# Generation Time: 2018-01-03 16:14:33 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table admin_video_images
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_video_images`;

CREATE TABLE `admin_video_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_video_id` int(11) NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_default` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table admin_videos
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_videos`;

CREATE TABLE `admin_videos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `details` text COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL,
  `video` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `video_subtitle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `trailer_video` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `trailer_subtitle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `default_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `banner_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ratings` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reviews` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_approved` int(11) NOT NULL,
  `is_home_slider` int(11) NOT NULL DEFAULT '0',
  `is_banner` int(11) NOT NULL,
  `uploaded_by` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `publish_time` datetime NOT NULL,
  `duration` time NOT NULL,
  `trailer_duration` time NOT NULL,
  `video_resolutions` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `trailer_video_resolutions` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `compress_status` int(11) NOT NULL DEFAULT '0',
  `trailer_compress_status` int(11) NOT NULL DEFAULT '0',
  `video_resize_path` longtext COLLATE utf8_unicode_ci,
  `trailer_resize_path` longtext COLLATE utf8_unicode_ci,
  `edited_by` enum('admin','moderator','user','other') COLLATE utf8_unicode_ci NOT NULL,
  `ppv_created_by` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `watch_count` int(11) NOT NULL,
  `type_of_user` int(11) NOT NULL DEFAULT '0',
  `type_of_subscription` int(11) NOT NULL DEFAULT '0',
  `amount` float NOT NULL DEFAULT '0',
  `redeem_amount` double(8,2) NOT NULL,
  `admin_amount` double(8,2) NOT NULL,
  `user_amount` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `video_type` int(11) NOT NULL,
  `video_upload_type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table admins
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admins`;

CREATE TABLE `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_activated` int(11) NOT NULL,
  `gender` enum('male','female','others') COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `paypal_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token_expiry` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `picture`, `description`, `is_activated`, `gender`, `mobile`, `paypal_email`, `address`, `token`, `token_expiry`, `remember_token`, `timezone`, `created_at`, `updated_at`)
VALUES
	(1,'Admin','admin@streamview.com','$2y$10$/cJcy52OOYqcV/we9Na//uVNIMmFkVXS7ttgzkY0dJQj.8v/aQb36','http://adminview.streamhash.com/placeholder.png','',0,'male','','','','','',NULL,'','2018-01-03 16:12:53','2018-01-03 16:12:53'),
	(2,'Test','test@streamview.com','$2y$10$q3Ns4/x1tEFnn02IciBfMe.GNSReEhKXE9UkkfWDPMfB8TFSh7qe6','http://adminview.streamhash.com/placeholder.png','',0,'male','','','','','',NULL,'','2018-01-03 16:12:53','2018-01-03 16:12:53');

/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cards
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cards`;

CREATE TABLE `cards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `customer_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_four` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `card_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_default` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_series` int(11) NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_approved` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table failed_jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table flags
# ------------------------------------------------------------

DROP TABLE IF EXISTS `flags`;

CREATE TABLE `flags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key, It is an unique key',
  `user_id` int(10) unsigned NOT NULL,
  `sub_profile_id` int(11) NOT NULL,
  `video_id` int(10) unsigned NOT NULL,
  `reason` longtext COLLATE utf8_unicode_ci COMMENT 'Reason for flagging the video',
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Status of the flag table',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table genres
# ------------------------------------------------------------

DROP TABLE IF EXISTS `genres`;

CREATE TABLE `genres` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `video` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `is_approved` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table languages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `languages`;

CREATE TABLE `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `folder_name` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table like_dislike_videos
# ------------------------------------------------------------

DROP TABLE IF EXISTS `like_dislike_videos`;

CREATE TABLE `like_dislike_videos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_video_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sub_profile_id` int(11) NOT NULL,
  `like_status` int(11) NOT NULL,
  `dislike_status` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;

INSERT INTO `migrations` (`migration`, `batch`)
VALUES
	('2014_10_12_000000_create_users_table',1),
	('2014_10_12_100000_create_password_resets_table',1),
	('2015_08_25_172600_create_settings_table',1),
	('2016_07_25_142335_create_admins_table',1),
	('2016_07_25_142358_create_moderators_table',1),
	('2016_07_28_111853_create_categories_table',1),
	('2016_07_28_111900_create_sub_categories_table',1),
	('2016_07_28_113237_create_sub_category_images_table',1),
	('2016_07_30_033554_add_is_series_field_to_categories_table',1),
	('2016_07_30_040800_create_admin_videos_table',1),
	('2016_07_30_040833_create_admin_video_images_table',1),
	('2016_07_30_132812_create_genres_table',1),
	('2016_07_31_140521_add_genre_id_to_admin_videos_table',1),
	('2016_08_01_151115_add_status_to_admin_videos_table',1),
	('2016_08_02_030955_add_is_approved_to_categories_table',1),
	('2016_08_02_031030_add_is_approved_to_sub_categories_table',1),
	('2016_08_02_031053_add_is_approved_genres_table',1),
	('2016_08_02_031301_add_is_approved_admin_videos_table',1),
	('2016_08_02_134552_create_user_ratings_table',1),
	('2016_08_02_143110_create_wishlists_table',1),
	('2016_08_02_144545_create_user_histories_table',1),
	('2016_08_02_152202_add_default_image_to_admin_videos_table',1),
	('2016_08_02_154250_add_watch_count_to_admin_videos_table',1),
	('2016_08_07_122712_create_pages_table',1),
	('2016_08_08_091037_add_publish_time_to_admin_videos_table',1),
	('2016_08_13_075844_add_video_type-to_admin_videos_table',1),
	('2016_08_13_083130_add_video_upload_type-to_admin_videos_table',1),
	('2016_08_14_042749_add_description_as_text_type',1),
	('2016_08_16_035007_add_is_moderator_to_users_table',1),
	('2016_08_16_070128_add_is_user_to_moderator_table',1),
	('2016_08_19_134019_create_user_payments_table',1),
	('2016_08_19_182650_add_is_paid_to_users',1),
	('2016_08_26_065631_add_duration_to_admin_videos',1),
	('2016_08_29_064138_change_device_type_in_users_table',1),
	('2016_08_29_073204_create_mobile_registers_table',1),
	('2016_08_29_082431_create_page_counters_table',1),
	('2016_08_31_194838_change_video_id_in_admin_video_images',1),
	('2016_09_02_133843_add_is_home_slider_to_admin_videos',1),
	('2016_09_15_070030_create_jobs_table',1),
	('2016_09_15_070051_create_failed_jobs_table',1),
	('2016_09_15_163652_add_is_banner_to_admin_videos_table',1),
	('2016_09_23_180525_add_push_status_users_table',1),
	('2016_09_29_103536_change_login_by_users',1),
	('2017_01_31_114409_create_user_tracks_table',1),
	('2017_03_21_144617_add_timezone_users_field',1),
	('2017_03_21_144742_add_timezone_moderators_field',1),
	('2017_03_21_144824_add_timezone_admins_field',1),
	('2017_03_22_124504_create_flags_table',1),
	('2017_03_23_093118_create_pay_per_views_table',1),
	('2017_03_23_100352_add_pay_per_view_fields_to_admin_videos_table',1),
	('2017_04_07_083733_add_email_verification_fields_to_users_table',1),
	('2017_04_12_085551_create_language_table',1),
	('2017_05_24_151437_create_redeems_table',1),
	('2017_05_24_161212_create_redeem_requests_table',1),
	('2017_07_03_110327_create_sub_profile',1),
	('2017_07_04_062546_added_subscription_field_in_user_payments_table',1),
	('2017_07_04_062857_create_subscription_table',1),
	('2017_07_04_063121_added_subscription_fields_in_users_table',1),
	('2017_07_04_145640_add_details_field_in_videos_table',1),
	('2017_07_08_072952_add_no_of_account_in_subscription_table',1),
	('2017_07_08_091332_added_video_fields_table_genres',1),
	('2017_07_08_105614_added_image_field_table_genres',1),
	('2017_07_13_082946_create_notification_table',1),
	('2017_07_29_115401_add_unique_id_in_admin_videos',1),
	('2017_08_07_133107_added_unique_id_in_genre_table',1),
	('2017_08_14_085732_added_subtitle_to_admin_videos',1),
	('2017_08_14_092159_create_like_dislike_videos',1),
	('2017_09_04_102357_added_enum_in_page',1),
	('2017_10_09_073405_create_card_table',1),
	('2017_10_09_145238_alter_table_in_videos',1),
	('2017_10_09_145431_added_created_by_in_payperview',1),
	('2017_10_10_065833_added_redeem_amount_in_admin_videos',1),
	('2017_10_10_131357_added_payments_in_admin_videos',1),
	('2017_10_10_131448_added_payments_in_moderators',1),
	('2017_10_11_092951_added_subtitle_in_genre_table',1),
	('2017_10_13_144508_added_card_id_in_users_table',1),
	('2017_10_14_071458_added_payment_mode_in_users_table',1),
	('2017_10_14_092354_added_sub_profile_id_in_spam_videos',1),
	('2017_11_26_055417_added_no_of_account_in_users',1),
	('2017_11_26_061536_created_user_logged_in_table',1),
	('2017_12_12_173534_changed_data_type_of_admin_video_amount',1),
	('2017_12_13_094327_added_fields_in_pay_perviews',1),
	('2017_12_22_182954_add_notes_to_user_payments_table',1),
	('2017_12_22_183016_add_notes_to_pay_per_views_table',1),
	('2017_12_27_074050_add_commission_fields_to_pay_per_views_table',1),
	('2017_12_27_085914_add_commission_spilit_details_to_redeems',1),
	('2017_12_28_094142_changed_data_type_of_redeem',1);

/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mobile_registers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mobile_registers`;

CREATE TABLE `mobile_registers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `count` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `mobile_registers` WRITE;
/*!40000 ALTER TABLE `mobile_registers` DISABLE KEYS */;

INSERT INTO `mobile_registers` (`id`, `type`, `count`, `created_at`, `updated_at`)
VALUES
	(1,'android',0,NULL,NULL),
	(2,'ios',0,NULL,NULL),
	(3,'web',0,NULL,NULL);

/*!40000 ALTER TABLE `mobile_registers` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table moderators
# ------------------------------------------------------------

DROP TABLE IF EXISTS `moderators`;

CREATE TABLE `moderators` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token_expiry` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_activated` int(11) NOT NULL,
  `is_user` int(11) NOT NULL,
  `gender` enum('male','female','others') COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `paypal_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `total` double(8,2) NOT NULL,
  `total_admin_amount` double(8,2) NOT NULL,
  `total_user_amount` double(8,2) NOT NULL,
  `paid_amount` double(8,2) NOT NULL,
  `remaining_amount` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `moderators_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `moderators` WRITE;
/*!40000 ALTER TABLE `moderators` DISABLE KEYS */;

INSERT INTO `moderators` (`id`, `name`, `email`, `password`, `token`, `token_expiry`, `picture`, `description`, `is_activated`, `is_user`, `gender`, `mobile`, `paypal_email`, `address`, `remember_token`, `timezone`, `total`, `total_admin_amount`, `total_user_amount`, `paid_amount`, `remaining_amount`, `created_at`, `updated_at`)
VALUES
	(1,'Moderator','moderator@streamview.com','$2y$10$Q/.vXo75nDC31A88nJLcuuDNIcc71wQxkhaA9NPpajwuPUX0AxZxS','2y10nZldcFumhOVAqGANqa7NeiqGHDTB0GdSwD2eWthFFKURxksQq8K','1514995974','http://adminview.streamhash.com/placeholder.png','',1,0,'male','','','',NULL,'',0.00,0.00,0.00,0.00,0.00,'2018-01-03 16:12:54','2018-01-03 16:12:54');

/*!40000 ALTER TABLE `moderators` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table notifications
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `admin_video_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table page_counters
# ------------------------------------------------------------

DROP TABLE IF EXISTS `page_counters`;

CREATE TABLE `page_counters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `count` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pages`;

CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heading` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('about','privacy','terms','contact','help','others') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;

INSERT INTO `pages` (`id`, `heading`, `description`, `type`, `created_at`, `updated_at`)
VALUES
	(1,'about','about','about','2018-01-03 16:12:54','2018-01-03 16:12:54'),
	(2,'contact','contact','contact','2018-01-03 16:12:54','2018-01-03 16:12:54'),
	(3,'privacy','privacy','privacy','2018-01-03 16:12:54','2018-01-03 16:12:54'),
	(4,'terms','terms','terms','2018-01-03 16:12:54','2018-01-03 16:12:54'),
	(5,'help','help','help','2018-01-03 16:12:54','2018-01-03 16:12:54');

/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table password_resets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table pay_per_views
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pay_per_views`;

CREATE TABLE `pay_per_views` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key, It is an unique key',
  `user_id` int(10) unsigned NOT NULL COMMENT 'User table Primary key given as Foreign Key',
  `video_id` int(10) unsigned NOT NULL COMMENT 'Admin Video table Primary key given as Foreign Key',
  `payment_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` double(8,2) NOT NULL,
  `admin_amount` double(8,2) NOT NULL,
  `moderator_amount` double(8,2) NOT NULL,
  `type_of_subscription` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type_of_user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expiry_date` datetime NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Status of the per_per_view table',
  `reason` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `currency` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table redeem_requests
# ------------------------------------------------------------

DROP TABLE IF EXISTS `redeem_requests`;

CREATE TABLE `redeem_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moderator_id` int(11) NOT NULL,
  `request_amount` double(8,2) NOT NULL,
  `paid_amount` double(8,2) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table redeems
# ------------------------------------------------------------

DROP TABLE IF EXISTS `redeems`;

CREATE TABLE `redeems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moderator_id` int(11) NOT NULL,
  `total` double(8,2) NOT NULL,
  `total_admin_amount` double(8,2) NOT NULL,
  `total_moderator_amount` double(8,2) NOT NULL,
  `paid` double(8,2) NOT NULL,
  `remaining` double(8,2) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `settings_key_index` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`)
VALUES
	(1,'site_name','StreamHash',NULL,NULL),
	(2,'site_logo','/logo.png',NULL,NULL),
	(3,'site_icon','/favicon.png',NULL,NULL),
	(4,'tag_name','',NULL,NULL),
	(5,'browser_key','',NULL,NULL),
	(6,'default_lang','en',NULL,NULL),
	(7,'currency','$',NULL,NULL),
	(8,'admin_delete_control','0',NULL,NULL),
	(9,'installation_process','0',NULL,NULL),
	(10,'amount','10',NULL,NULL),
	(11,'expiry_days','28',NULL,NULL),
	(12,'admin_take_count','12',NULL,NULL),
	(13,'google_analytics','',NULL,NULL),
	(14,'streaming_url','',NULL,NULL),
	(15,'video_compress_size','50',NULL,NULL),
	(16,'image_compress_size','8',NULL,NULL),
	(17,'s3_bucket','',NULL,NULL),
	(18,'track_user_mail','',NULL,NULL),
	(19,'REPORT_VIDEO','Sexual content',NULL,NULL),
	(20,'REPORT_VIDEO','Violent or repulsive content.',NULL,NULL),
	(21,'REPORT_VIDEO','Hateful or abusive content.',NULL,NULL),
	(22,'REPORT_VIDEO','Harmful dangerous acts.',NULL,NULL),
	(23,'REPORT_VIDEO','Child abuse.',NULL,NULL),
	(24,'REPORT_VIDEO','Spam or misleading.',NULL,NULL),
	(25,'REPORT_VIDEO','Infringes my rights.',NULL,NULL),
	(26,'REPORT_VIDEO','Captions issue.',NULL,NULL),
	(27,'VIDEO_RESOLUTIONS','426x240',NULL,NULL),
	(28,'VIDEO_RESOLUTIONS','640x360',NULL,NULL),
	(29,'VIDEO_RESOLUTIONS','854x480',NULL,NULL),
	(30,'VIDEO_RESOLUTIONS','1280x720',NULL,NULL),
	(31,'VIDEO_RESOLUTIONS','1920x1080',NULL,NULL),
	(32,'email_verify_control','0',NULL,NULL),
	(33,'is_subscription','1',NULL,NULL),
	(34,'is_spam','1',NULL,NULL),
	(35,'is_payper_view','1',NULL,NULL),
	(36,'admin_language_control','1','2018-01-03 16:12:53','2018-01-03 16:12:53'),
	(37,'appstore','',NULL,NULL),
	(38,'playstore','',NULL,NULL),
	(39,'home_page_bg_image','/images/home_page_bg_image.jpg','2018-01-03 16:12:53','2018-01-03 16:12:53'),
	(40,'common_bg_image','/images/login-bg.jpg','2018-01-03 16:12:53','2018-01-03 16:12:53'),
	(41,'header_scripts','',NULL,NULL),
	(42,'body_scripts','',NULL,NULL),
	(43,'ANGULAR_SITE_URL','',NULL,NULL),
	(44,'JWPLAYER_KEY','M2NCefPoiiKsaVB8nTttvMBxfb1J3Xl7PDXSaw==','2018-01-03 16:12:53','2018-01-03 16:12:53'),
	(45,'HLS_STREAMING_URL','','2018-01-03 16:12:53','2018-01-03 16:12:53'),
	(46,'demo_admin_email','','2018-01-03 16:12:53','2018-01-03 16:12:53'),
	(47,'demo_admin_password','','2018-01-03 16:12:53','2018-01-03 16:12:53'),
	(48,'post_max_size','2000M',NULL,NULL),
	(49,'upload_max_size','2000M',NULL,NULL),
	(50,'stripe_publishable_key','pk_test_uDYrTXzzAuGRwDYtu7dkhaF3',NULL,NULL),
	(51,'stripe_secret_key','sk_test_lRUbYflDyRP3L2UbnsehTUHW',NULL,NULL),
	(52,'video_viewer_count','10','2018-01-03 16:12:54','2018-01-03 16:12:54'),
	(53,'amount_per_video','100','2018-01-03 16:12:54','2018-01-03 16:12:54'),
	(54,'minimum_redeem','1','2018-01-03 16:12:54','2018-01-03 16:12:54'),
	(55,'redeem_control','1','2018-01-03 16:12:54','2018-01-03 16:12:54'),
	(56,'admin_commission','10','2018-01-03 16:12:54','2018-01-03 16:12:54'),
	(57,'user_commission','90','2018-01-03 16:12:54','2018-01-03 16:12:54'),
	(58,'facebook_link','',NULL,NULL),
	(59,'linkedin_link','',NULL,NULL),
	(60,'twitter_link','',NULL,NULL),
	(61,'google_plus_link','',NULL,NULL),
	(62,'pinterest_link','',NULL,NULL),
	(63,'token_expiry_hour','1',NULL,NULL);

/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sub_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sub_categories`;

CREATE TABLE `sub_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_approved` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table sub_category_images
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sub_category_images`;

CREATE TABLE `sub_category_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_category_id` int(11) NOT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table sub_profiles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sub_profiles`;

CREATE TABLE `sub_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `sub_profiles` WRITE;
/*!40000 ALTER TABLE `sub_profiles` DISABLE KEYS */;

INSERT INTO `sub_profiles` (`id`, `user_id`, `name`, `picture`, `status`, `created_at`, `updated_at`)
VALUES
	(1,1,'User','http://adminview.streamhash.com/placeholder.png',1,'2018-01-03 16:12:54','2018-01-03 16:12:54'),
	(2,2,'Test','http://adminview.streamhash.com/placeholder.png',1,'2018-01-03 16:12:54','2018-01-03 16:12:54');

/*!40000 ALTER TABLE `sub_profiles` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table subscriptions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `subscriptions`;

CREATE TABLE `subscriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `subscription_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'month,year,days',
  `plan` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` double(8,2) NOT NULL,
  `total_subscription` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `popular_status` int(11) NOT NULL DEFAULT '0',
  `no_of_account` int(11) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table user_histories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_histories`;

CREATE TABLE `user_histories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `admin_video_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table user_logged_devices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_logged_devices`;

CREATE TABLE `user_logged_devices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token_expiry` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table user_payments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_payments`;

CREATE TABLE `user_payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` double(8,2) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `reason` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table user_ratings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_ratings`;

CREATE TABLE `user_ratings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `admin_video_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table user_tracks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_tracks`;

CREATE TABLE `user_tracks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` text COLLATE utf8_unicode_ci NOT NULL,
  `HTTP_USER_AGENT` text COLLATE utf8_unicode_ci NOT NULL,
  `REQUEST_TIME` text COLLATE utf8_unicode_ci NOT NULL,
  `REMOTE_ADDR` text COLLATE utf8_unicode_ci NOT NULL,
  `hostname` text COLLATE utf8_unicode_ci NOT NULL,
  `latitude` double(10,8) NOT NULL,
  `longitude` double(10,8) NOT NULL,
  `origin` text COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `region` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `others` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `view` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token_expiry` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `device_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `device_type` enum('android','ios','web') COLLATE utf8_unicode_ci NOT NULL,
  `login_by` enum('manual','facebook','twitter','google','linkedin') COLLATE utf8_unicode_ci NOT NULL,
  `social_unique_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fb_lg` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gl_lg` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_activated` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `no_of_account` int(11) NOT NULL,
  `logged_in_account` int(11) NOT NULL,
  `card_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payment_mode` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `verification_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `verification_code_expiry` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_verified` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `push_status` int(11) NOT NULL,
  `user_type` int(11) NOT NULL,
  `is_moderator` int(11) NOT NULL,
  `moderator_id` int(11) NOT NULL,
  `gender` enum('male','female','others') COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `latitude` double(15,8) NOT NULL,
  `longitude` double(15,8) NOT NULL,
  `paypal_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount_paid` double(8,2) NOT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `no_of_days` int(11) NOT NULL,
  `one_time_subscription` int(11) NOT NULL DEFAULT '0' COMMENT '0 - Not Subscribed , 1 - Subscribed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `picture`, `token`, `token_expiry`, `device_token`, `device_type`, `login_by`, `social_unique_id`, `fb_lg`, `gl_lg`, `description`, `is_activated`, `status`, `no_of_account`, `logged_in_account`, `card_id`, `payment_mode`, `verification_code`, `verification_code_expiry`, `is_verified`, `push_status`, `user_type`, `is_moderator`, `moderator_id`, `gender`, `mobile`, `latitude`, `longitude`, `paypal_email`, `address`, `remember_token`, `timezone`, `amount_paid`, `expiry_date`, `no_of_days`, `one_time_subscription`, `created_at`, `updated_at`)
VALUES
	(1,'User','user@streamview.com','$2y$10$7ejdd.9sfrOJUi0dDdOyAOU/QtURw4slUpUpqe2rW2Wo34HBN77jm','http://adminview.streamhash.com/placeholder.png','2y10iocAjucdV6n4bbOfcWgKEmA1b8sb2wxYnjczTsCWkgc7qNC','1514995973','','web','manual','','','','',1,1,0,0,'','','','','1',0,1,0,0,'male','',0.00000000,0.00000000,'','',NULL,'',0.00,NULL,0,0,'2018-01-03 16:12:53','2018-01-03 16:12:53'),
	(2,'Test','test@streamview.com','$2y$10$5nbc1zfWfdgqg4Pv9SC5zeUpch8YBHXtcGq7e678AxvYlnQ92wLE2','http://adminview.streamhash.com/placeholder.png','2y100shde5zbU57isfOMcxxXrn8vyF8vD6oLwPKbNS4JQqrk7RzfgMQS','1514995974','','web','manual','','','','',1,1,0,0,'','','','','1',0,1,0,0,'male','',0.00000000,0.00000000,'','',NULL,'',0.00,NULL,0,0,'2018-01-03 16:12:54','2018-01-03 16:12:54');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wishlists
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wishlists`;

CREATE TABLE `wishlists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `admin_video_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
