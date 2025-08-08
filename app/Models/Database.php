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
                skor INT,
                klasifikasi TEXT,
                keyakinan SMALLINT,
                praktik SMALLINT,
                pengalaman SMALLINT,
                perasaan VARCHAR(100),
                resume_terapis TEXT,
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
                ('xyz', 'Speaker-Anggrek1-101', 'Gedung Anggrek', 'Lantai 1', 'Ruang Rawat 101', NOW(), NOW()),
                ('g1u', 'Speaker-Mawar-01', 'Gedung Mawar', 'Lantai 1', 'Ruang Rawat 101', NOW(), NOW()),
                ('p2k', 'Speaker-Mawar-02', 'Gedung Mawar', 'Lantai 1', 'Ruang Rawat 102', NOW(), NOW()),
                ('q3j', 'Speaker-Mawar-03', 'Gedung Mawar', 'Lantai 1', 'Ruang Rawat 103', NOW(), NOW()),
                ('l4i', 'Speaker-Mawar-04', 'Gedung Mawar', 'Lantai 2', 'Ruang Rawat 201', NOW(), NOW()),
                ('m5h', 'Speaker-Mawar-05', 'Gedung Mawar', 'Lantai 2', 'Ruang Rawat 202', NOW(), NOW()),
                ('f6g', 'Speaker-Mawar-06', 'Gedung Mawar', 'Lantai 2', 'Ruang Rawat 203', NOW(), NOW()),
                ('e7f', 'Speaker-Anggrek-02', 'Gedung Anggrek', 'Lantai 3', 'Ruang Rawat 301', NOW(), NOW()),
                ('d8e', 'Speaker-Anggrek-03', 'Gedung Anggrek', 'Lantai 3', 'Ruang Rawat 302', NOW(), NOW()),
                ('c9d', 'Speaker-Anggrek-04', 'Gedung Anggrek', 'Lantai 3', 'Ruang Rawat 303', NOW(), NOW()),
                ('b1c', 'Speaker-Kenanga-01', 'Gedung Kenanga', 'Lantai 4', 'Ruang Tunggu 401', NOW(), NOW()),
                ('a2b', 'Speaker-Kenanga-02', 'Gedung Kenanga', 'Lantai 4', 'Ruang Tunggu 402', NOW(), NOW()),
                ('z3a', 'Speaker-Kenanga-03', 'Gedung Kenanga', 'Lantai 4', 'Ruang Tunggu 403', NOW(), NOW()),
                ('y4z', 'Speaker-Teratai-01', 'Gedung Teratai', 'Lantai 5', 'Ruang Operasi 1', NOW(), NOW()),
                ('x5y', 'Speaker-Teratai-02', 'Gedung Teratai', 'Lantai 5', 'Ruang Operasi 2', NOW(), NOW()),
                ('w6x', 'Speaker-Teratai-03', 'Gedung Teratai', 'Lantai 5', 'Ruang Operasi 3', NOW(), NOW()),
                ('v7w', 'Speaker-Auditorium-01', 'Gedung Rektorat', 'Lantai 1', 'Ruang Auditorium', NOW(), NOW()),
                ('u8v', 'Speaker-Pertemuan-01', 'Gedung Rektorat', 'Lantai 1', 'Ruang Pertemuan', NOW(), NOW())
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
