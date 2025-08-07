<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Database
{
    static function create() : void {
        DB::unprepared("
            CREATE TABLE IF NOT EXISTS ref_agama (
                id SMALLINT PRIMARY KEY,
                nama_agama VARCHAR(255),
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                deleted_at DATETIME DEFAULT NULL
            );

            CREATE TABLE IF NOT EXISTS ms_ruang (
                id CHAR(3) PRIMARY KEY,
                nama_device VARCHAR(255),
                gedung VARCHAR(100),
                lantai VARCHAR(100),
                kamar VARCHAR(100),
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                deleted_at DATETIME DEFAULT NULL
            );

            CREATE TABLE IF NOT EXISTS ms_role (
                id CHAR(1) PRIMARY KEY,
                nama_role TEXT,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,
                deleted_at TIMESTAMP DEFAULT NULL
            );

            CREATE TABLE IF NOT EXISTS users (
                id CHAR(36) PRIMARY KEY,
                username VARCHAR(100),
                password VARCHAR(60),
                nama VARCHAR(100),
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,
                deleted_at TIMESTAMP DEFAULT NULL
            );

            CREATE TABLE IF NOT EXISTS user_role (
                id CHAR(36) PRIMARY KEY,
                user_id CHAR(36), FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE ON DELETE SET NULL,
                role_id CHAR(1), FOREIGN KEY (role_id) REFERENCES ms_role (id) ON UPDATE CASCADE ON DELETE SET NULL,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,
                deleted_at TIMESTAMP DEFAULT NULL
            );

            CREATE TABLE IF NOT EXISTS pasien (
                id CHAR(36) PRIMARY KEY,
                nama_pasien VARCHAR(100),
                agama_id SMALLINT, FOREIGN KEY (agama_id) REFERENCES ref_agama (id) ON UPDATE CASCADE ON DELETE SET NULL,
                ruang_id CHAR(3), FOREIGN KEY (ruang_id) REFERENCES ms_ruang (id) ON UPDATE CASCADE ON DELETE SET NULL,
                tanggal_lahir DATE,
                jenis_kelamin CHAR(1),
                first_assesmen CHAR(36),
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,
                deleted_at TIMESTAMP DEFAULT NULL
            );

            CREATE TABLE IF NOT EXISTS assesmen (
                id CHAR(36) PRIMARY KEY,
                pasien_id CHAR(36), FOREIGN KEY (pasien_id) REFERENCES pasien (id) ON UPDATE CASCADE ON DELETE SET NULL,
                c1 CHAR(1),
                e1 TEXT,
                perasaan VARCHAR(100),
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,
                deleted_at TIMESTAMP DEFAULT NULL
            );
        ");
    }

    static function store() : void {
        DB::unprepared("
            INSERT INTO ref_agama(id, nama_agama, created_at, updated_at) VALUES
                (1,'Islam',NOW(),NOW()),
                (2,'Kristen',NOW(),NOW()),
                (3,'Katolik',NOW(),NOW()),
                (4,'Hindu',NOW(),NOW()),
                (5,'Budha',NOW(),NOW()),
                (6,'Khonghucu',NOW(),NOW()),
                (99,'Lainnya',NOW(),NOW())
            ON DUPLICATE KEY UPDATE id=id;

            INSERT INTO users(id, username, password, nama, created_at, updated_at) VALUES
                ('b9489f22-4751-44b2-8004-55a05a92adbe', 'a', '\$2a\$10\$gdtRVkD9YVxOmUozU65odOO8Q3DF8wuhlWx17KVkpQQ6aWj8UZqCu', 'The Nurse', NOW(), NOW())
            ON DUPLICATE KEY UPDATE id=id;

            INSERT INTO ms_role(id, nama_role, created_at, updated_at) VALUES
                ('P', 'PERAWAT', NOW(), NOW())
            ON DUPLICATE KEY UPDATE id=id;

            INSERT INTO user_role(id, user_id, role_id, created_at, updated_at) VALUES
                ('b9489f22-4751-44b2-8004-55a05a92adbe', 'b9489f22-4751-44b2-8004-55a05a92adbe', 'P', NOW(), NOW())
            ON DUPLICATE KEY UPDATE id=id;

            INSERT INTO ms_ruang(id, nama_device, gedung, lantai, kamar, created_at, updated_at) VALUES
                ('xyz', 'Speaker-Anggrek1-101', 'Gedung Anggrek', 'Anggrek 1', '101', NOW(), NOW())
            ON DUPLICATE KEY UPDATE id=id;
        ");
    }

    static function destroy() : void {
        DB::unprepared("
            DROP TABLE IF EXISTS assesmen;
            DROP TABLE IF EXISTS pasien;
            DROP TABLE IF EXISTS user_role;
            DROP TABLE IF EXISTS users;
            DROP TABLE IF EXISTS ms_role;
            DROP TABLE IF EXISTS ms_ruang;
            DROP TABLE IF EXISTS ref_agama;
        ");
    }
}
