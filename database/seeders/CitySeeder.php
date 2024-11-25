<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $EgyptCities=[
        "Cairo"
        ,"Giza"
        ,"Alexandria"
        ,"Madīnat as Sādis min Uktūbar"
        ,"Shubrā al Khaymah"
        ,"Al Manşūrah"
        ,"Ḩalwān"
        ,"Al Maḩallah al Kubrá"
        ,"Port Said"
        ,"Suez"
        ,"Ţanţā"
        ,"Asyūţ"
        ,"Al Fayyūm"
        ,"Az Zaqāzīq"
        ,"Ismailia"
        ,"Aswān"
        ,"Kafr ad Dawwār"
        ,"Damanhūr"
        ,"Al Minyā"
        ,"Damietta"
        ,"Luxor"
        ,"Qinā"
        ,"Sūhāj"
        ,"Banī Suwayf"
        ,"Shibīn al Kawm"
        ,"Al ‘Arīsh"
        ,"Al Ghardaqah"
        ,"Banhā"
        ,"Kafr ash Shaykh"
        ,"Disūq"
        ,"Bilbays"
        ,"Mallawī"
        ,"Idfū"
        ,"Mīt Ghamr"
        ,"Munūf"
        ,"Jirjā"
        ,"Akhmīm"
        ,"Ziftá"
        ,"Samālūţ"
        ,"Manfalūţ"
        ,"Banī Mazār"
        ,"Armant"
        ,"Maghāghah"
        ,"Kawm Umbū"
        ,"Būr Fu’ād"
        ,"Al Qūşīyah"
        ,"Rosetta"
        ,"Isnā"
        ,"Maţrūḩ"
        ,"Abnūb"
        ,"Hihyā"
        ,"Samannūd"
        ,"Dandarah"
        ,"Al Khārjah"
        ,"Al Balyanā"
        ,"Maţāy"
        ,"Naj‘ Ḩammādī"
        ,"Şān al Ḩajar al Qiblīyah"
        ,"Dayr Mawās"
        ,"Ihnāsyā al Madīnah"
        ,"Darāw"
        ,"Abū Qīr"
        ,"Fāraskūr"
        ,"Ra’s Ghārib"
        ,"Al Ḩusaynīyah"
        ,"Safājā"
        ,"Qiman al ‘Arūs"
        ,"Qahā"
        ,"Al Karnak"
        ,"Hirrīyat Raznah"
        ,"Al Quşayr"
        ,"Kafr Shukr"
        ,"Sīwah"
        ,"Kafr Sa‘d"
        ,"Shārūnah"
        ,"Aţ Ţūr"
        ,"Rafaḩ"
        ,"Ash Shaykh Zuwayd"
        ,"Bi’r al ‘Abd"
        ];
        $Libya=[
            "Tripoli",
            "Benghazi",
            "Mişrātah",
            "Al Bayḑā’",
            "Al Khums",
            "Az Zāwīyah",
            "Gharyān",
            "Al Marj",
            "Ajdābiyā",
            "Tobruk",
            "Darnah",
            "Sabhā",
            "Şabrātah",
            "Zuwārah",
            "Surt",
            "Yafran",
            "Nālūt",
            "Banī Walīd",
            "Tājūrā’",
            "Murzuq",
            "Birāk",
            "Shaḩḩāt",
            "Mizdah",
            "Al Jawf",
            "Ghāt",
            "Hūn",
            "Al ‘Azīzīyah",
            "Awbārī",
            "Idrī",
            "Al Kufrah",

        ];
        $KSACities=[
            "Riyadh",
            "Jeddah",
            "Mecca",
            "Medina",
            "Ad Dammām",
            "Al Hufūf",
            "Buraydah",
            "Al Ḩillah",
            "Aţ Ţā’if",
            "Tabūk",
            "Khamīs Mushayţ",
            "Ḩā’il",
            "Al Qaţīf",
            "Al Mubarraz",
            "Al Kharj",
            "Najrān",
            "Yanbu‘",
            "Abhā",
            "Arar",
            "Jāzān",
            "Sakākā",
            "Al Bāḩah",

        ];
        
        foreach ($EgyptCities as $city) {
            DB::table('cities')->insert([
                'city' => $city,
                'country_id' => 1,
            ]);
        }
        foreach ($Libya as $city) {
            DB::table('cities')->insert([
                'city' => $city,
                'country_id' => 2,
            ]);
        }
        foreach ($KSACities as $city) {
            DB::table('cities')->insert([
                'city' => $city,
                'country_id' => 3,
            ]);
        }
        
    }
}
