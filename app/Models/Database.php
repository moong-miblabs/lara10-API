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

            CREATE TABLE IF NOT EXISTS ms_device (
                id CHAR(3) PRIMARY KEY,
                nama_device VARCHAR(100),
                kasur VARCHAR(100),
                kamar VARCHAR(100),
                lantai VARCHAR(100),
                gedung VARCHAR(100),
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                deleted_at DATETIME DEFAULT NULL
            );

            CREATE TABLE IF NOT EXISTS ms_audio (
                id CHAR(36) PRIMARY KEY,
                agama_id SMALLINT,
                kategori VARCHAR(255),
                title VARCHAR(255),
                artist VARCHAR(255),
                album VARCHAR(255),
                artwork_src VARCHAR(255),
                artwork_sizes VARCHAR(255) DEFAULT '512x512',
                artwork_type VARCHAR(255) DEFAULT 'image/jpg',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                deleted_at DATETIME DEFAULT NULL
            );

            CREATE TABLE IF NOT EXISTS pasien (
                id CHAR(36) PRIMARY KEY,
                pin CHAR(6),
                nama_pasien VARCHAR(100),
                agama_id SMALLINT, FOREIGN KEY (agama_id) REFERENCES ref_agama (id) ON UPDATE CASCADE ON DELETE SET NULL,
                device_id CHAR(3), FOREIGN KEY (device_id) REFERENCES ms_device (id) ON UPDATE CASCADE ON DELETE SET NULL,
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
                keyakinan_c1 CHAR(1),
                keyakinan_e1 TEXT,
                praktik_c1 CHAR(1),
                praktik_e1 TEXT,
                pengalaman_c1 CHAR(1),
                pengalaman_e1 TEXT,
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

            CREATE TABLE IF NOT EXISTS master_to_slave (
                id CHAR(3) PRIMARY KEY,
                pasien_id CHAR(36),
                audio_id CHAR(36),
                `on` DATETIME,
                `off` DATETIME,
                `light` BOOLEAN
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

            INSERT INTO ms_device(id, kasur, nama_device, gedung, lantai, kamar, created_at, updated_at) VALUES
                ('xyz', '1', 'Speaker-Anggrek1-101', 'Gedung Anggrek', 'Lantai 1', 'Ruang Rawat 101', NOW(), NOW()),
                ('g1u', '1', 'Speaker-Mawar-01', 'Gedung Mawar', 'Lantai 1', 'Ruang Rawat 101', NOW(), NOW()),
                ('p2k', '1', 'Speaker-Mawar-02', 'Gedung Mawar', 'Lantai 1', 'Ruang Rawat 102', NOW(), NOW()),
                ('q3j', '1', 'Speaker-Mawar-03', 'Gedung Mawar', 'Lantai 1', 'Ruang Rawat 103', NOW(), NOW()),
                ('l4i', '1', 'Speaker-Mawar-04', 'Gedung Mawar', 'Lantai 2', 'Ruang Rawat 201', NOW(), NOW()),
                ('m5h', '1', 'Speaker-Mawar-05', 'Gedung Mawar', 'Lantai 2', 'Ruang Rawat 202', NOW(), NOW()),
                ('f6g', '1', 'Speaker-Mawar-06', 'Gedung Mawar', 'Lantai 2', 'Ruang Rawat 203', NOW(), NOW()),
                ('e7f', '1', 'Speaker-Anggrek-02', 'Gedung Anggrek', 'Lantai 3', 'Ruang Rawat 301', NOW(), NOW()),
                ('d8e', '1', 'Speaker-Anggrek-03', 'Gedung Anggrek', 'Lantai 3', 'Ruang Rawat 302', NOW(), NOW()),
                ('c9d', '1', 'Speaker-Anggrek-04', 'Gedung Anggrek', 'Lantai 3', 'Ruang Rawat 303', NOW(), NOW()),
                ('b1c', '1', 'Speaker-Kenanga-01', 'Gedung Kenanga', 'Lantai 4', 'Ruang Tunggu 401', NOW(), NOW()),
                ('a2b', '1', 'Speaker-Kenanga-02', 'Gedung Kenanga', 'Lantai 4', 'Ruang Tunggu 402', NOW(), NOW()),
                ('z3a', '1', 'Speaker-Kenanga-03', 'Gedung Kenanga', 'Lantai 4', 'Ruang Tunggu 403', NOW(), NOW()),
                ('y4z', '1', 'Speaker-Teratai-01', 'Gedung Teratai', 'Lantai 5', 'Ruang Operasi 1', NOW(), NOW()),
                ('x5y', '1', 'Speaker-Teratai-02', 'Gedung Teratai', 'Lantai 5', 'Ruang Operasi 2', NOW(), NOW()),
                ('w6x', '1', 'Speaker-Teratai-03', 'Gedung Teratai', 'Lantai 5', 'Ruang Operasi 3', NOW(), NOW()),
                ('v7w', '1', 'Speaker-Auditorium-01', 'Gedung Rektorat', 'Lantai 1', 'Ruang Auditorium', NOW(), NOW()),
                ('u8v', '1', 'Speaker-Pertemuan-01', 'Gedung Rektorat', 'Lantai 1', 'Ruang Pertemuan', NOW(), NOW())
            ON DUPLICATE KEY UPDATE id=id;

            INSERT INTO pasien(id,pin,nama_pasien,agama_id,device_id,tanggal_lahir,jenis_kelamin,created_at,updated_at) VALUES
                ('abc','123456','MUNJI HANAFI',1,'xyz','1993-06-10','L',NOW(),NOW())
            ON DUPLICATE KEY UPDATE id=id;

            INSERT INTO ms_audio (id,agama_id,kategori,title,artist,album,artwork_src,created_at,updated_at) VALUES 
                (UUID(),1,'Sholawat','Syi\'ir Tanpo Waton','Gus Mochammad Nizam','Sholawat','Sholawat tanpa musik YA RASSULLALLAH SALAMUN ALAIK nada Syiir tanpo waton Gus Dur tanpa musik.mp3',NOW(),NOW()),
                (UUID(),1,'Sholawat','Maula ya salli wa sallim daiman abadan','M Tariq & M Yusuf','Sholawat','Maula ya salli wa sallim daiman abadan_ Muhammad Tariq & Muhammad Yusuf Medly.mp3',NOW(),NOW()),
                (UUID(),1,'Zikir','Tarhim','Syeikh Mahmud Khalil Al-Hushariy','Zikir','SHOLAWAT TARHIM DI SEBUAH DESA MENJELANG MAGHRIB.mp3',NOW(),NOW()),
                (UUID(),1,'Zikir','Mahalul Qiyam','Majlis Nurul Musthafa','Zikir','NURUL MUSTHOFA - MAHALUL QIYAM BASS KOTEK NYA AJIBBBB.mp3',NOW(),NOW())
            ON DUPLICATE KEY UPDATE id=id;
        ");
    }

    static function destroy() : void {
        DB::unprepared("
            DROP TABLE IF EXISTS master_to_slave;
            DROP TABLE IF EXISTS assesmen;
            DROP TABLE IF EXISTS pasien;
            DROP TABLE IF EXISTS user_role;
            DROP TABLE IF EXISTS users;
            DROP TABLE IF EXISTS ms_role;
            DROP TABLE IF EXISTS ms_audio;
            DROP TABLE IF EXISTS ms_device;
            DROP TABLE IF EXISTS ref_agama;
        ");
    }
}
