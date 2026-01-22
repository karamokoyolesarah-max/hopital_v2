<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ExternalDoctorsSeeder extends Seeder
{
    public function run()
    {
        $doctors = [
            [
                'first_name' => 'Jean',
                'last_name' => 'Kouassi',
                'email' => 'j.kouassi@hospitsis.ci',
                'phone' => '+225 07 12 34 56 78',
                'password' => Hash::make('password'),
                'speciality' => 'Cardiologie',
                'license_number' => 'MED-CI-2024-00001',
                'qualifications' => "Doctorat en Médecine - Université Félix Houphouët-Boigny (2010)\nSpécialisation en Cardiologie - CHU de Treichville (2015)\nDiplôme Européen de Cardiologie - Paris (2018)",
                'bio' => "Cardiologue expérimenté avec plus de 15 ans d'expérience. Spécialisé dans le traitement des maladies cardiovasculaires, l'hypertension et les troubles du rythme cardiaque.",
                'status' => 'approved',
                'is_active' => true,
                'consultation_fee' => 35000.00,
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Diallo',
                'email' => 'dr.diallo@hospitsis.com',
                'phone' => '+225 07 23 45 67 89',
                'password' => Hash::make('password'),
                'speciality' => 'Pédiatrie',
                'license_number' => 'MED-CI-2024-00002',
                'qualifications' => "Doctorat en Médecine - Université d'Abidjan (2012)\nSpécialisation en Pédiatrie - CHU de Yopougon (2016)\nFormation en Néonatologie - France (2019)",
                'bio' => "Pédiatre passionnée dédiée à la santé et au bien-être des enfants. Plus de 10 ans d'expérience dans le suivi médical des nourrissons, enfants et adolescents.",
                'status' => 'approved',
                'is_active' => true,
                'consultation_fee' => 25000.00,
            ],
            [
                'first_name' => 'Amadou',
                'last_name' => 'Traoré',
                'email' => 'dr.traore@hospitsis.com',
                'phone' => '+225 07 34 56 78 90',
                'password' => Hash::make('password'),
                'speciality' => 'Gynécologie',
                'license_number' => 'MED-CI-2024-00003',
                'qualifications' => "Doctorat en Médecine - Université de Cocody (2011)\nSpécialisation en Gynécologie-Obstétrique - CHU de Cocody (2016)\nMaster en Chirurgie Gynécologique - Dakar (2018)",
                'bio' => "Gynécologue-obstétricien spécialisé dans le suivi de grossesse, l'accouchement et la santé reproductive féminine.",
                'status' => 'approved',
                'is_active' => true,
                'consultation_fee' => 30000.00,
            ],
            [
                'first_name' => 'Fatou',
                'last_name' => 'Koné',
                'email' => 'dr.kone@hospitsis.com',
                'phone' => '+225 07 45 67 89 01',
                'password' => Hash::make('password'),
                'speciality' => 'Dermatologie',
                'license_number' => 'MED-CI-2024-00004',
                'qualifications' => "Doctorat en Médecine - Université d'Abidjan (2014)\nSpécialisation en Dermatologie - CHU de Treichville (2018)",
                'bio' => "Dermatologue spécialisée dans le traitement des affections cutanées, l'acné, l'eczéma et les soins esthétiques.",
                'status' => 'pending',
                'is_active' => true,
                'consultation_fee' => 28000.00,
            ],
            [
                'first_name' => 'Ibrahim',
                'last_name' => 'Sanogo',
                'email' => 'dr.sanogo@hospitsis.com',
                'phone' => '+225 07 56 78 90 12',
                'password' => Hash::make('password'),
                'speciality' => 'Médecine générale',
                'license_number' => 'MED-CI-2024-00005',
                'qualifications' => "Doctorat en Médecine - Université Félix Houphouët-Boigny (2013)\nMédecine Générale - CHU de Yopougon (2016)",
                'bio' => "Médecin généraliste polyvalent offrant des consultations pour toutes les pathologies courantes.",
                'status' => 'approved',
                'is_active' => true,
                'consultation_fee' => 20000.00,
            ],
            [
                'first_name' => 'Awa',
                'last_name' => 'Bamba',
                'email' => 'dr.bamba@hospitsis.com',
                'phone' => '+225 07 78 90 12 34',
                'password' => Hash::make('password'),
                'speciality' => 'Ophtalmologie',
                'license_number' => 'MED-CI-2024-00007',
                'qualifications' => "Doctorat en Médecine - Université de Cocody (2012)\nSpécialisation en Ophtalmologie - CHU de Treichville (2017)",
                'bio' => "Ophtalmologue expérimentée spécialisée dans les troubles de la vision, le traitement de la cataracte et le glaucome.",
                'status' => 'approved',
                'is_active' => true,
                'consultation_fee' => 32000.00,
            ],
            [
                'first_name' => 'Seydou',
                'last_name' => 'Camara',
                'email' => 'dr.camara@hospitsis.com',
                'phone' => '+225 07 89 01 23 45',
                'password' => Hash::make('password'),
                'speciality' => 'Psychiatrie',
                'license_number' => 'MED-CI-2024-00008',
                'qualifications' => "Doctorat en Médecine - Université Félix Houphouët-Boigny (2011)\nSpécialisation en Psychiatrie - CHU de Yopougon (2016)",
                'bio' => "Psychiatre et psychothérapeute spécialisé dans le traitement des troubles anxieux, dépressifs et bipolaires.",
                'status' => 'approved',
                'is_active' => true,
                'consultation_fee' => 45000.00,
            ],
        ];

        foreach ($doctors as $doctor) {
            DB::table('external_doctors')->insert([
                'first_name' => $doctor['first_name'],
                'last_name' => $doctor['last_name'],
                'email' => $doctor['email'],
                'phone' => $doctor['phone'],
                'password' => $doctor['password'],
                'speciality' => $doctor['speciality'],
                'license_number' => $doctor['license_number'],
                'qualifications' => $doctor['qualifications'],
                'bio' => $doctor['bio'],
                'status' => $doctor['status'],
                'is_active' => $doctor['is_active'],
                'consultation_fee' => $doctor['consultation_fee'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}