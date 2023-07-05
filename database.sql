-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- 생성 시간: 23-05-25 08:09
-- 서버 버전: 10.6.12-MariaDB-0ubuntu0.22.04.1
-- PHP 버전: 8.1.2-1ubuntu2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `ccnara`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `account`
--

CREATE TABLE `account` (
  `Num` int(11) NOT NULL COMMENT '고유번호',
  `username` text NOT NULL COMMENT '사용자 아이디',
  `password` text NOT NULL COMMENT '사용자 비밀번호'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 테이블 구조 `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL COMMENT '공급사 고유번호',
  `name` text DEFAULT NULL COMMENT '공급사 이름',
  `phone` text DEFAULT NULL COMMENT '공급사 전화번호',
  `email` text DEFAULT NULL COMMENT '공급사 Email 주소',
  `fax` text DEFAULT NULL COMMENT 'Fax 번호',
  `fax_order` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Fax 발주 여부',
  `memo` text DEFAULT NULL COMMENT '공급사 메모'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`Num`);

--
-- 테이블의 인덱스 `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `account`
--
ALTER TABLE `account`
  MODIFY `Num` int(11) NOT NULL AUTO_INCREMENT COMMENT '고유번호';

--
-- 테이블의 AUTO_INCREMENT `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '공급사 고유번호';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
