<?php

namespace App\Services;

class AirportService
{
    public static function all(): array
    {
        return [
            // USA
            ['code' => 'ATL', 'city' => 'Atlanta', 'country' => 'USA', 'name' => 'Hartsfield-Jackson'],
            ['code' => 'BOS', 'city' => 'Boston', 'country' => 'USA', 'name' => 'Logan'],
            ['code' => 'BWI', 'city' => 'Baltimore', 'country' => 'USA', 'name' => 'Baltimore/Washington'],
            ['code' => 'CLT', 'city' => 'Charlotte', 'country' => 'USA', 'name' => 'Douglas'],
            ['code' => 'DCA', 'city' => 'Washington DC', 'country' => 'USA', 'name' => 'Reagan National'],
            ['code' => 'DEN', 'city' => 'Denver', 'country' => 'USA', 'name' => 'Denver'],
            ['code' => 'DFW', 'city' => 'Dallas', 'country' => 'USA', 'name' => 'Fort Worth'],
            ['code' => 'DTW', 'city' => 'Detroit', 'country' => 'USA', 'name' => 'Metropolitan'],
            ['code' => 'EWR', 'city' => 'Newark', 'country' => 'USA', 'name' => 'Newark Liberty'],
            ['code' => 'FLL', 'city' => 'Fort Lauderdale', 'country' => 'USA', 'name' => 'Fort Lauderdale'],
            ['code' => 'HNL', 'city' => 'Honolulu', 'country' => 'USA', 'name' => 'Daniel K Inouye'],
            ['code' => 'IAD', 'city' => 'Washington DC', 'country' => 'USA', 'name' => 'Dulles'],
            ['code' => 'IAH', 'city' => 'Houston', 'country' => 'USA', 'name' => 'George Bush'],
            ['code' => 'JFK', 'city' => 'New York', 'country' => 'USA', 'name' => 'John F Kennedy'],
            ['code' => 'LAS', 'city' => 'Las Vegas', 'country' => 'USA', 'name' => 'McCarran'],
            ['code' => 'LAX', 'city' => 'Los Angeles', 'country' => 'USA', 'name' => 'Los Angeles'],
            ['code' => 'LGA', 'city' => 'New York', 'country' => 'USA', 'name' => 'LaGuardia'],
            ['code' => 'MCO', 'city' => 'Orlando', 'country' => 'USA', 'name' => 'Orlando'],
            ['code' => 'MDW', 'city' => 'Chicago', 'country' => 'USA', 'name' => 'Midway'],
            ['code' => 'MIA', 'city' => 'Miami', 'country' => 'USA', 'name' => 'Miami'],
            ['code' => 'MSP', 'city' => 'Minneapolis', 'country' => 'USA', 'name' => 'St Paul'],
            ['code' => 'ORD', 'city' => 'Chicago', 'country' => 'USA', 'name' => "O'Hare"],
            ['code' => 'PHL', 'city' => 'Philadelphia', 'country' => 'USA', 'name' => 'Philadelphia'],
            ['code' => 'PHX', 'city' => 'Phoenix', 'country' => 'USA', 'name' => 'Sky Harbor'],
            ['code' => 'SAN', 'city' => 'San Diego', 'country' => 'USA', 'name' => 'San Diego'],
            ['code' => 'SEA', 'city' => 'Seattle', 'country' => 'USA', 'name' => 'Seattle-Tacoma'],
            ['code' => 'SFO', 'city' => 'San Francisco', 'country' => 'USA', 'name' => 'San Francisco'],
            ['code' => 'SLC', 'city' => 'Salt Lake City', 'country' => 'USA', 'name' => 'Salt Lake City'],
            ['code' => 'TPA', 'city' => 'Tampa', 'country' => 'USA', 'name' => 'Tampa'],
            ['code' => 'AUS', 'city' => 'Austin', 'country' => 'USA', 'name' => 'Bergstrom'],
            ['code' => 'BNA', 'city' => 'Nashville', 'country' => 'USA', 'name' => 'Nashville'],
            ['code' => 'MCI', 'city' => 'Kansas City', 'country' => 'USA', 'name' => 'Kansas City'],
            ['code' => 'PDX', 'city' => 'Portland', 'country' => 'USA', 'name' => 'Portland'],
            ['code' => 'RDU', 'city' => 'Raleigh', 'country' => 'USA', 'name' => 'Durham'],
            ['code' => 'STL', 'city' => 'St Louis', 'country' => 'USA', 'name' => 'Lambert'],
            ['code' => 'PIT', 'city' => 'Pittsburgh', 'country' => 'USA', 'name' => 'Pittsburgh'],
            ['code' => 'IND', 'city' => 'Indianapolis', 'country' => 'USA', 'name' => 'Indianapolis'],
            ['code' => 'CMH', 'city' => 'Columbus', 'country' => 'USA', 'name' => 'John Glenn'],
            ['code' => 'CVG', 'city' => 'Cincinnati', 'country' => 'USA', 'name' => 'Northern Kentucky'],
            ['code' => 'BUF', 'city' => 'Buffalo', 'country' => 'USA', 'name' => 'Niagara'],
            ['code' => 'MSY', 'city' => 'New Orleans', 'country' => 'USA', 'name' => 'Louis Armstrong'],
            ['code' => 'SAT', 'city' => 'San Antonio', 'country' => 'USA', 'name' => 'San Antonio'],
            ['code' => 'SMF', 'city' => 'Sacramento', 'country' => 'USA', 'name' => 'Sacramento'],
            ['code' => 'SJC', 'city' => 'San Jose', 'country' => 'USA', 'name' => 'Norman Mineta'],
            ['code' => 'OAK', 'city' => 'Oakland', 'country' => 'USA', 'name' => 'Metropolitan Oakland'],
            ['code' => 'PBI', 'city' => 'West Palm Beach', 'country' => 'USA', 'name' => 'Palm Beach'],
            ['code' => 'RSW', 'city' => 'Fort Myers', 'country' => 'USA', 'name' => 'Southwest Florida'],
            ['code' => 'JAX', 'city' => 'Jacksonville', 'country' => 'USA', 'name' => 'Jacksonville'],
            ['code' => 'ABQ', 'city' => 'Albuquerque', 'country' => 'USA', 'name' => 'Albuquerque'],
            ['code' => 'TUS', 'city' => 'Tucson', 'country' => 'USA', 'name' => 'Tucson'],
            ['code' => 'OKC', 'city' => 'Oklahoma City', 'country' => 'USA', 'name' => 'Will Rogers'],
            ['code' => 'MEM', 'city' => 'Memphis', 'country' => 'USA', 'name' => 'Memphis'],

            // Canada
            ['code' => 'YYZ', 'city' => 'Toronto', 'country' => 'Canada', 'name' => 'Pearson'],
            ['code' => 'YUL', 'city' => 'Montreal', 'country' => 'Canada', 'name' => 'Trudeau'],
            ['code' => 'YVR', 'city' => 'Vancouver', 'country' => 'Canada', 'name' => 'Vancouver'],
            ['code' => 'YYC', 'city' => 'Calgary', 'country' => 'Canada', 'name' => 'Calgary'],
            ['code' => 'YEG', 'city' => 'Edmonton', 'country' => 'Canada', 'name' => 'Edmonton'],
            ['code' => 'YOW', 'city' => 'Ottawa', 'country' => 'Canada', 'name' => 'Macdonald-Cartier'],
            ['code' => 'YHZ', 'city' => 'Halifax', 'country' => 'Canada', 'name' => 'Stanfield'],

            // UK
            ['code' => 'LHR', 'city' => 'London', 'country' => 'UK', 'name' => 'Heathrow'],
            ['code' => 'LGW', 'city' => 'London', 'country' => 'UK', 'name' => 'Gatwick'],
            ['code' => 'STN', 'city' => 'London', 'country' => 'UK', 'name' => 'Stansted'],
            ['code' => 'LTN', 'city' => 'London', 'country' => 'UK', 'name' => 'Luton'],
            ['code' => 'LCY', 'city' => 'London', 'country' => 'UK', 'name' => 'City'],
            ['code' => 'MAN', 'city' => 'Manchester', 'country' => 'UK', 'name' => 'Manchester'],
            ['code' => 'BHX', 'city' => 'Birmingham', 'country' => 'UK', 'name' => 'Birmingham'],
            ['code' => 'GLA', 'city' => 'Glasgow', 'country' => 'UK', 'name' => 'Glasgow'],
            ['code' => 'EDI', 'city' => 'Edinburgh', 'country' => 'UK', 'name' => 'Edinburgh'],
            ['code' => 'BFS', 'city' => 'Belfast', 'country' => 'UK', 'name' => 'Belfast'],
            ['code' => 'NCL', 'city' => 'Newcastle', 'country' => 'UK', 'name' => 'Newcastle'],
            ['code' => 'LPL', 'city' => 'Liverpool', 'country' => 'UK', 'name' => 'John Lennon'],

            // UAE
            ['code' => 'DXB', 'city' => 'Dubai', 'country' => 'UAE', 'name' => 'Dubai'],
            ['code' => 'AUH', 'city' => 'Abu Dhabi', 'country' => 'UAE', 'name' => 'Zayed'],
            ['code' => 'SHJ', 'city' => 'Sharjah', 'country' => 'UAE', 'name' => 'Sharjah'],

            // Turkey
            ['code' => 'IST', 'city' => 'Istanbul', 'country' => 'Turkey', 'name' => 'Istanbul'],
            ['code' => 'SAW', 'city' => 'Istanbul', 'country' => 'Turkey', 'name' => 'Sabiha Gokcen'],
            ['code' => 'AYT', 'city' => 'Antalya', 'country' => 'Turkey', 'name' => 'Antalya'],
            ['code' => 'ESB', 'city' => 'Ankara', 'country' => 'Turkey', 'name' => 'Esenboga'],
            ['code' => 'ADB', 'city' => 'Izmir', 'country' => 'Turkey', 'name' => 'Adnan Menderes'],
            ['code' => 'DLM', 'city' => 'Dalaman', 'country' => 'Turkey', 'name' => 'Dalaman'],
            ['code' => 'BJV', 'city' => 'Bodrum', 'country' => 'Turkey', 'name' => 'Milas-Bodrum'],

            // Middle East
            ['code' => 'DOH', 'city' => 'Doha', 'country' => 'Qatar', 'name' => 'Hamad'],
            ['code' => 'JED', 'city' => 'Jeddah', 'country' => 'Saudi Arabia', 'name' => 'King Abdulaziz'],
            ['code' => 'RUH', 'city' => 'Riyadh', 'country' => 'Saudi Arabia', 'name' => 'King Khalid'],
            ['code' => 'DMM', 'city' => 'Dammam', 'country' => 'Saudi Arabia', 'name' => 'King Fahd'],
            ['code' => 'MED', 'city' => 'Medina', 'country' => 'Saudi Arabia', 'name' => 'Prince Mohammad'],
            ['code' => 'BAH', 'city' => 'Bahrain', 'country' => 'Bahrain', 'name' => 'Bahrain'],
            ['code' => 'MCT', 'city' => 'Muscat', 'country' => 'Oman', 'name' => 'Muscat'],
            ['code' => 'KWI', 'city' => 'Kuwait City', 'country' => 'Kuwait', 'name' => 'Kuwait'],
            ['code' => 'AMM', 'city' => 'Amman', 'country' => 'Jordan', 'name' => 'Queen Alia'],
            ['code' => 'BEY', 'city' => 'Beirut', 'country' => 'Lebanon', 'name' => 'Rafic Hariri'],

            // Egypt
            ['code' => 'CAI', 'city' => 'Cairo', 'country' => 'Egypt', 'name' => 'Cairo'],
            ['code' => 'HRG', 'city' => 'Hurghada', 'country' => 'Egypt', 'name' => 'Hurghada'],
            ['code' => 'SSH', 'city' => 'Sharm el Sheikh', 'country' => 'Egypt', 'name' => 'Sharm el Sheikh'],
            ['code' => 'LXR', 'city' => 'Luxor', 'country' => 'Egypt', 'name' => 'Luxor'],
            ['code' => 'ASW', 'city' => 'Aswan', 'country' => 'Egypt', 'name' => 'Aswan'],
            ['code' => 'SPX', 'city' => 'Sphinx', 'country' => 'Egypt', 'name' => 'Sphinx International'],

            // Germany
            ['code' => 'FRA', 'city' => 'Frankfurt', 'country' => 'Germany', 'name' => 'Frankfurt'],
            ['code' => 'MUC', 'city' => 'Munich', 'country' => 'Germany', 'name' => 'Munich'],
            ['code' => 'BER', 'city' => 'Berlin', 'country' => 'Germany', 'name' => 'Brandenburg'],
            ['code' => 'HAM', 'city' => 'Hamburg', 'country' => 'Germany', 'name' => 'Hamburg'],
            ['code' => 'DUS', 'city' => 'Dusseldorf', 'country' => 'Germany', 'name' => 'Dusseldorf'],
            ['code' => 'CGN', 'city' => 'Cologne', 'country' => 'Germany', 'name' => 'Cologne/Bonn'],
            ['code' => 'STR', 'city' => 'Stuttgart', 'country' => 'Germany', 'name' => 'Stuttgart'],
            ['code' => 'HAJ', 'city' => 'Hanover', 'country' => 'Germany', 'name' => 'Hanover'],
            ['code' => 'LEJ', 'city' => 'Leipzig', 'country' => 'Germany', 'name' => 'Leipzig/Halle'],

            // France
            ['code' => 'CDG', 'city' => 'Paris', 'country' => 'France', 'name' => 'Charles de Gaulle'],
            ['code' => 'ORY', 'city' => 'Paris', 'country' => 'France', 'name' => 'Orly'],
            ['code' => 'NCE', 'city' => 'Nice', 'country' => 'France', 'name' => 'Cote d Azur'],
            ['code' => 'MRS', 'city' => 'Marseille', 'country' => 'France', 'name' => 'Provence'],
            ['code' => 'LYS', 'city' => 'Lyon', 'country' => 'France', 'name' => 'Saint-Exupery'],
            ['code' => 'TLS', 'city' => 'Toulouse', 'country' => 'France', 'name' => 'Blagnac'],

            // Italy
            ['code' => 'FCO', 'city' => 'Rome', 'country' => 'Italy', 'name' => 'Fiumicino'],
            ['code' => 'CIA', 'city' => 'Rome', 'country' => 'Italy', 'name' => 'Ciampino'],
            ['code' => 'MXP', 'city' => 'Milan', 'country' => 'Italy', 'name' => 'Malpensa'],
            ['code' => 'BGY', 'city' => 'Milan', 'country' => 'Italy', 'name' => 'Bergamo'],
            ['code' => 'VCE', 'city' => 'Venice', 'country' => 'Italy', 'name' => 'Marco Polo'],
            ['code' => 'NAP', 'city' => 'Naples', 'country' => 'Italy', 'name' => 'Capodichino'],
            ['code' => 'FLR', 'city' => 'Florence', 'country' => 'Italy', 'name' => 'Peretola'],
            ['code' => 'BLQ', 'city' => 'Bologna', 'country' => 'Italy', 'name' => 'Guglielmo Marconi'],
            ['code' => 'PSA', 'city' => 'Pisa', 'country' => 'Italy', 'name' => 'Galileo Galilei'],
            ['code' => 'CTA', 'city' => 'Catania', 'country' => 'Italy', 'name' => 'Fontanarossa'],

            // Spain
            ['code' => 'MAD', 'city' => 'Madrid', 'country' => 'Spain', 'name' => 'Barajas'],
            ['code' => 'BCN', 'city' => 'Barcelona', 'country' => 'Spain', 'name' => 'El Prat'],
            ['code' => 'AGP', 'city' => 'Malaga', 'country' => 'Spain', 'name' => 'Costa del Sol'],
            ['code' => 'PMI', 'city' => 'Palma', 'country' => 'Spain', 'name' => 'Palma de Mallorca'],
            ['code' => 'IBZ', 'city' => 'Ibiza', 'country' => 'Spain', 'name' => 'Ibiza'],
            ['code' => 'ALC', 'city' => 'Alicante', 'country' => 'Spain', 'name' => 'Alicante-Elche'],
            ['code' => 'VLC', 'city' => 'Valencia', 'country' => 'Spain', 'name' => 'Valencia'],
            ['code' => 'SVQ', 'city' => 'Seville', 'country' => 'Spain', 'name' => 'San Pablo'],

            // Netherlands
            ['code' => 'AMS', 'city' => 'Amsterdam', 'country' => 'Netherlands', 'name' => 'Schiphol'],
            ['code' => 'RTM', 'city' => 'Rotterdam', 'country' => 'Netherlands', 'name' => 'The Hague'],

            // Switzerland
            ['code' => 'ZRH', 'city' => 'Zurich', 'country' => 'Switzerland', 'name' => 'Zurich'],
            ['code' => 'GVA', 'city' => 'Geneva', 'country' => 'Switzerland', 'name' => 'Geneva'],
            ['code' => 'BSL', 'city' => 'Basel', 'country' => 'Switzerland', 'name' => 'EuroAirport'],

            // Rest of Europe
            ['code' => 'VIE', 'city' => 'Vienna', 'country' => 'Austria', 'name' => 'Vienna'],
            ['code' => 'BRU', 'city' => 'Brussels', 'country' => 'Belgium', 'name' => 'Brussels'],
            ['code' => 'CPH', 'city' => 'Copenhagen', 'country' => 'Denmark', 'name' => 'Kastrup'],
            ['code' => 'ARN', 'city' => 'Stockholm', 'country' => 'Sweden', 'name' => 'Arlanda'],
            ['code' => 'OSL', 'city' => 'Oslo', 'country' => 'Norway', 'name' => 'Gardermoen'],
            ['code' => 'HEL', 'city' => 'Helsinki', 'country' => 'Finland', 'name' => 'Vantaa'],
            ['code' => 'WAW', 'city' => 'Warsaw', 'country' => 'Poland', 'name' => 'Chopin'],
            ['code' => 'KRK', 'city' => 'Krakow', 'country' => 'Poland', 'name' => 'John Paul II'],
            ['code' => 'PRG', 'city' => 'Prague', 'country' => 'Czech Republic', 'name' => 'Vaclav Havel'],
            ['code' => 'BUD', 'city' => 'Budapest', 'country' => 'Hungary', 'name' => 'Ferenc Liszt'],
            ['code' => 'OTP', 'city' => 'Bucharest', 'country' => 'Romania', 'name' => 'Henri Coanda'],
            ['code' => 'SOF', 'city' => 'Sofia', 'country' => 'Bulgaria', 'name' => 'Sofia'],
            ['code' => 'ATH', 'city' => 'Athens', 'country' => 'Greece', 'name' => 'Eleftherios Venizelos'],
            ['code' => 'SKG', 'city' => 'Thessaloniki', 'country' => 'Greece', 'name' => 'Makedonia'],
            ['code' => 'HER', 'city' => 'Heraklion', 'country' => 'Greece', 'name' => 'Nikos Kazantzakis'],
            ['code' => 'RHO', 'city' => 'Rhodes', 'country' => 'Greece', 'name' => 'Diagoras'],
            ['code' => 'ZAG', 'city' => 'Zagreb', 'country' => 'Croatia', 'name' => 'Franjo Tudman'],
            ['code' => 'BEG', 'city' => 'Belgrade', 'country' => 'Serbia', 'name' => 'Nikola Tesla'],
            ['code' => 'TIA', 'city' => 'Tirana', 'country' => 'Albania', 'name' => 'Nene Tereza'],
            ['code' => 'DUB', 'city' => 'Dublin', 'country' => 'Ireland', 'name' => 'Dublin'],
            ['code' => 'SNN', 'city' => 'Shannon', 'country' => 'Ireland', 'name' => 'Shannon'],
            ['code' => 'LIS', 'city' => 'Lisbon', 'country' => 'Portugal', 'name' => 'Humberto Delgado'],
            ['code' => 'OPO', 'city' => 'Porto', 'country' => 'Portugal', 'name' => 'Francisco Sa Carneiro'],
            ['code' => 'ZRH', 'city' => 'Zurich', 'country' => 'Switzerland', 'name' => 'Zurich'],
            ['code' => 'GVA', 'city' => 'Geneva', 'country' => 'Switzerland', 'name' => 'Geneva'],

            // Russia & CIS
            ['code' => 'SVO', 'city' => 'Moscow', 'country' => 'Russia', 'name' => 'Sheremetyevo'],
            ['code' => 'DME', 'city' => 'Moscow', 'country' => 'Russia', 'name' => 'Domodedovo'],
            ['code' => 'VKO', 'city' => 'Moscow', 'country' => 'Russia', 'name' => 'Vnukovo'],
            ['code' => 'LED', 'city' => 'St Petersburg', 'country' => 'Russia', 'name' => 'Pulkovo'],
            ['code' => 'KBP', 'city' => 'Kyiv', 'country' => 'Ukraine', 'name' => 'Boryspil'],
            ['code' => 'EVN', 'city' => 'Yerevan', 'country' => 'Armenia', 'name' => 'Zvartnots'],
            ['code' => 'TBS', 'city' => 'Tbilisi', 'country' => 'Georgia', 'name' => 'Tbilisi'],

            // Asia
            ['code' => 'NRT', 'city' => 'Tokyo', 'country' => 'Japan', 'name' => 'Narita'],
            ['code' => 'HND', 'city' => 'Tokyo', 'country' => 'Japan', 'name' => 'Haneda'],
            ['code' => 'KIX', 'city' => 'Osaka', 'country' => 'Japan', 'name' => 'Kansai'],
            ['code' => 'NGO', 'city' => 'Nagoya', 'country' => 'Japan', 'name' => 'Chubu'],
            ['code' => 'ICN', 'city' => 'Seoul', 'country' => 'South Korea', 'name' => 'Incheon'],
            ['code' => 'GMP', 'city' => 'Seoul', 'country' => 'South Korea', 'name' => 'Gimpo'],
            ['code' => 'PEK', 'city' => 'Beijing', 'country' => 'China', 'name' => 'Capital'],
            ['code' => 'PKX', 'city' => 'Beijing', 'country' => 'China', 'name' => 'Daxing'],
            ['code' => 'PVG', 'city' => 'Shanghai', 'country' => 'China', 'name' => 'Pudong'],
            ['code' => 'SHA', 'city' => 'Shanghai', 'country' => 'China', 'name' => 'Hongqiao'],
            ['code' => 'CAN', 'city' => 'Guangzhou', 'country' => 'China', 'name' => 'Baiyun'],
            ['code' => 'SZX', 'city' => 'Shenzhen', 'country' => 'China', 'name' => 'Baoan'],
            ['code' => 'HKG', 'city' => 'Hong Kong', 'country' => 'China', 'name' => 'Chek Lap Kok'],
            ['code' => 'MFM', 'city' => 'Macau', 'country' => 'China', 'name' => 'Macau'],
            ['code' => 'TPE', 'city' => 'Taipei', 'country' => 'Taiwan', 'name' => 'Taoyuan'],
            ['code' => 'BKK', 'city' => 'Bangkok', 'country' => 'Thailand', 'name' => 'Suvarnabhumi'],
            ['code' => 'DMK', 'city' => 'Bangkok', 'country' => 'Thailand', 'name' => 'Don Mueang'],
            ['code' => 'CNX', 'city' => 'Chiang Mai', 'country' => 'Thailand', 'name' => 'Chiang Mai'],
            ['code' => 'HKT', 'city' => 'Phuket', 'country' => 'Thailand', 'name' => 'Phuket'],
            ['code' => 'SIN', 'city' => 'Singapore', 'country' => 'Singapore', 'name' => 'Changi'],
            ['code' => 'KUL', 'city' => 'Kuala Lumpur', 'country' => 'Malaysia', 'name' => 'Kuala Lumpur'],
            ['code' => 'CGK', 'city' => 'Jakarta', 'country' => 'Indonesia', 'name' => 'Soekarno-Hatta'],
            ['code' => 'DPS', 'city' => 'Bali', 'country' => 'Indonesia', 'name' => 'Ngurah Rai'],
            ['code' => 'MNL', 'city' => 'Manila', 'country' => 'Philippines', 'name' => 'Ninoy Aquino'],
            ['code' => 'HAN', 'city' => 'Hanoi', 'country' => 'Vietnam', 'name' => 'Noi Bai'],
            ['code' => 'SGN', 'city' => 'Ho Chi Minh City', 'country' => 'Vietnam', 'name' => 'Tan Son Nhat'],
            ['code' => 'DAD', 'city' => 'Da Nang', 'country' => 'Vietnam', 'name' => 'Da Nang'],
            ['code' => 'RGN', 'city' => 'Yangon', 'country' => 'Myanmar', 'name' => 'Yangon'],
            ['code' => 'PNH', 'city' => 'Phnom Penh', 'country' => 'Cambodia', 'name' => 'Phnom Penh'],
            ['code' => 'REP', 'city' => 'Siem Reap', 'country' => 'Cambodia', 'name' => 'Angkor'],
            ['code' => 'DAC', 'city' => 'Dhaka', 'country' => 'Bangladesh', 'name' => 'Shahjalal'],
            ['code' => 'KTM', 'city' => 'Kathmandu', 'country' => 'Nepal', 'name' => 'Tribhuvan'],
            ['code' => 'CMB', 'city' => 'Colombo', 'country' => 'Sri Lanka', 'name' => 'Bandaranaike'],
            ['code' => 'MLE', 'city' => 'Male', 'country' => 'Maldives', 'name' => 'Velana'],
            ['code' => 'DEL', 'city' => 'Delhi', 'country' => 'India', 'name' => 'Indira Gandhi'],
            ['code' => 'BOM', 'city' => 'Mumbai', 'country' => 'India', 'name' => 'Chhatrapati Shivaji'],
            ['code' => 'BLR', 'city' => 'Bangalore', 'country' => 'India', 'name' => 'Kempegowda'],
            ['code' => 'MAA', 'city' => 'Chennai', 'country' => 'India', 'name' => 'Chennai'],
            ['code' => 'CCU', 'city' => 'Kolkata', 'country' => 'India', 'name' => 'Netaji Subhas'],
            ['code' => 'HYD', 'city' => 'Hyderabad', 'country' => 'India', 'name' => 'Rajiv Gandhi'],
            ['code' => 'COK', 'city' => 'Kochi', 'country' => 'India', 'name' => 'Cochin'],

            // Africa
            ['code' => 'JNB', 'city' => 'Johannesburg', 'country' => 'South Africa', 'name' => 'OR Tambo'],
            ['code' => 'CPT', 'city' => 'Cape Town', 'country' => 'South Africa', 'name' => 'Cape Town'],
            ['code' => 'DUR', 'city' => 'Durban', 'country' => 'South Africa', 'name' => 'King Shaka'],
            ['code' => 'LOS', 'city' => 'Lagos', 'country' => 'Nigeria', 'name' => 'Murtala Muhammed'],
            ['code' => 'ABV', 'city' => 'Abuja', 'country' => 'Nigeria', 'name' => 'Nnamdi Azikiwe'],
            ['code' => 'ADD', 'city' => 'Addis Ababa', 'country' => 'Ethiopia', 'name' => 'Bole'],
            ['code' => 'NBO', 'city' => 'Nairobi', 'country' => 'Kenya', 'name' => 'Jomo Kenyatta'],
            ['code' => 'DAR', 'city' => 'Dar es Salaam', 'country' => 'Tanzania', 'name' => 'Julius Nyerere'],
            ['code' => 'ACC', 'city' => 'Accra', 'country' => 'Ghana', 'name' => 'Kotoka'],
            ['code' => 'CMN', 'city' => 'Casablanca', 'country' => 'Morocco', 'name' => 'Mohammed V'],
            ['code' => 'RAK', 'city' => 'Marrakech', 'country' => 'Morocco', 'name' => 'Menara'],
            ['code' => 'TUN', 'city' => 'Tunis', 'country' => 'Tunisia', 'name' => 'Tunis-Carthage'],
            ['code' => 'ALG', 'city' => 'Algiers', 'country' => 'Algeria', 'name' => 'Houari Boumediene'],
            ['code' => 'RBA', 'city' => 'Rabat', 'country' => 'Morocco', 'name' => 'Rabat-Sale'],

            // Australia / NZ
            ['code' => 'SYD', 'city' => 'Sydney', 'country' => 'Australia', 'name' => 'Kingsford Smith'],
            ['code' => 'MEL', 'city' => 'Melbourne', 'country' => 'Australia', 'name' => 'Tullamarine'],
            ['code' => 'BNE', 'city' => 'Brisbane', 'country' => 'Australia', 'name' => 'Brisbane'],
            ['code' => 'PER', 'city' => 'Perth', 'country' => 'Australia', 'name' => 'Perth'],
            ['code' => 'ADL', 'city' => 'Adelaide', 'country' => 'Australia', 'name' => 'Adelaide'],
            ['code' => 'AKL', 'city' => 'Auckland', 'country' => 'New Zealand', 'name' => 'Auckland'],
            ['code' => 'CHC', 'city' => 'Christchurch', 'country' => 'New Zealand', 'name' => 'Christchurch'],
            ['code' => 'WLG', 'city' => 'Wellington', 'country' => 'New Zealand', 'name' => 'Wellington'],

            // South America
            ['code' => 'GRU', 'city' => 'Sao Paulo', 'country' => 'Brazil', 'name' => 'Guarulhos'],
            ['code' => 'GIG', 'city' => 'Rio de Janeiro', 'country' => 'Brazil', 'name' => 'Galeao'],
            ['code' => 'BSB', 'city' => 'Brasilia', 'country' => 'Brazil', 'name' => 'Presidente Juscelino'],
            ['code' => 'EZE', 'city' => 'Buenos Aires', 'country' => 'Argentina', 'name' => 'Ezeiza'],
            ['code' => 'AEP', 'city' => 'Buenos Aires', 'country' => 'Argentina', 'name' => 'Aeroparque'],
            ['code' => 'SCL', 'city' => 'Santiago', 'country' => 'Chile', 'name' => 'Arturo Merino Benitez'],
            ['code' => 'LIM', 'city' => 'Lima', 'country' => 'Peru', 'name' => 'Jorge Chavez'],
            ['code' => 'BOG', 'city' => 'Bogota', 'country' => 'Colombia', 'name' => 'El Dorado'],
            ['code' => 'MEX', 'city' => 'Mexico City', 'country' => 'Mexico', 'name' => 'Benito Juarez'],
            ['code' => 'CUN', 'city' => 'Cancun', 'country' => 'Mexico', 'name' => 'Cancun'],
            ['code' => 'PTY', 'city' => 'Panama City', 'country' => 'Panama', 'name' => 'Tocumen'],
            ['code' => 'SJO', 'city' => 'San Jose', 'country' => 'Costa Rica', 'name' => 'Juan Santamaria'],
            ['code' => 'HAV', 'city' => 'Havana', 'country' => 'Cuba', 'name' => 'Jose Marti'],
            ['code' => 'PUJ', 'city' => 'Punta Cana', 'country' => 'Dominican Republic', 'name' => 'Punta Cana'],
            ['code' => 'SDQ', 'city' => 'Santo Domingo', 'country' => 'Dominican Republic', 'name' => 'Las Americas'],
        ];
    }

    public static function search(string $query): array
    {
        if (strlen($query) < 1) return [];
        $q = strtoupper($query);
        return array_values(array_filter(self::all(), fn($a) =>
            str_starts_with($a['code'], $q) ||
            str_starts_with(strtoupper($a['city']), $q) ||
            str_starts_with(strtoupper($a['name']), $q) ||
            str_contains(strtoupper($a['city']), $q) ||
            str_contains(strtoupper($a['country']), $q)
        ));
    }

    public static function toJson(): string
    {
        return json_encode(self::all());
    }
}