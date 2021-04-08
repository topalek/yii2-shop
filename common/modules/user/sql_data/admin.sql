-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 04, 2014 at 11:54 AM
-- Server version: 5.5.40-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.5

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`, `role`, `status`, `auth_key`, `access_token`, `updated_at`,
                    `created_at`, `deleted_at`)
VALUES (1, 'admin', '$2y$13$Xeho97wOCw5WNolYUQBsE.Y218re5K2kmPpZtH68TfstsJOnbfNFm', 'admin@gmail.com', 'admin', 1,
        'NrmwHdntFKHOeTEXaWVCN0kZbeH4z97T', NULL, NULL, NULL, NULL);
