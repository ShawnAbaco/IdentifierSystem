<?php
// database/seeders/SpeciesSeeder.php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    public function run()
    {
        $treeCategory = Category::where('slug', 'trees')->first();
        $flowerCategory = Category::where('slug', 'flowers')->first();

        // Philippine Native Trees
        $trees = [
            [
                'common_name' => 'Narra',
                'scientific_name' => 'Pterocarpus indicus',
                'description' => 'Narra is a large deciduous tree and the national tree of the Philippines. It is known for its hard, durable wood that ranges in color from red to yellow-brown. The tree produces fragrant, yellow flowers and can grow up to 30-40 meters tall.',
                'characteristics' => [
                    'Height: 30-40 meters',
                    'Trunk diameter: Up to 2 meters',
                    'Leaves: Compound, 12-22 cm long',
                    'Flowers: Yellow, fragrant',
                    'Fruit: Winged pod, 5-8 cm in diameter'
                ],
                'habitat' => 'Primary and secondary forests, often near rivers and streams',
                'conservation_status' => 'Vulnerable',
                'fun_facts' => [
                    'The Narra tree can live for over 100 years',
                    'Its wood is naturally resistant to termites and decay',
                    'The name "Narra" comes from the Spanish word for the tree',
                    'It is the national tree of the Philippines'
                ],
                'medicinal_uses' => [
                    'Bark used for treating diarrhea',
                    'Leaves used in traditional medicine for wounds',
                    'Wood extracts have anti-inflammatory properties',
                    'Used in herbal preparations for blood circulation'
                ],
                'cultural_significance' => [
                    'Considered sacred in some Filipino cultures',
                    'Used in traditional furniture making',
                    'Symbol of national pride and resilience',
                    'Often planted in parks and public places'
                ]
            ],
            [
                'common_name' => 'Mahogany',
                'scientific_name' => 'Swietenia macrophylla',
                'description' => 'A large tropical tree known for its durable, reddish-brown wood. It is widely planted in the Philippines for reforestation and timber production. The tree can reach heights of up to 60 meters and is valued for its straight trunk and beautiful grain.',
                'characteristics' => [
                    'Height: Up to 60 meters',
                    'Trunk diameter: Up to 3.5 meters',
                    'Leaves: Pinnate, 40-60 cm long',
                    'Bark: Dark gray, smooth when young',
                    'Fruit: Large woody capsule',
                    'Wood: Reddish-brown, durable'
                ],
                'habitat' => 'Tropical rainforests, planted in reforestation areas',
                'conservation_status' => 'Vulnerable',
                'fun_facts' => [
                    'Mahogany is one of the most valuable timber species in the world',
                    'The wood is naturally resistant to rot and insects',
                    'Can take up to 25 years to reach harvestable size',
                    'Used in making high-end furniture and musical instruments'
                ],
                'medicinal_uses' => [
                    'Bark used for treating fever',
                    'Seeds used for digestive issues',
                    'Oil extracted from seeds for skin conditions',
                    'Leaves used in traditional medicine for diabetes'
                ],
                'cultural_significance' => [
                    'Highly prized for making furniture',
                    'Used in boat building',
                    'Popular in reforestation projects',
                    'Symbol of strength and durability'
                ]
            ],
            [
                'common_name' => 'Acacia',
                'scientific_name' => 'Acacia mangium',
                'description' => 'Fast-growing tropical tree, widely used in reforestation projects and for timber production. It is known for its ability to grow in poor soil conditions and improve soil fertility through nitrogen fixation.',
                'characteristics' => [
                    'Height: 25-30 meters',
                    'Trunk diameter: 50-80 cm',
                    'Leaves: Phyllodes (modified leaf stalks)',
                    'Bark: Rough, fissured',
                    'Growth rate: Very fast, up to 4 meters per year'
                ],
                'habitat' => 'Lowland tropics, degraded lands',
                'conservation_status' => 'Least Concern',
                'fun_facts' => [
                    'Grows very quickly - up to 4 meters per year',
                    'Improves soil fertility by fixing nitrogen',
                    'Bark is used in traditional medicine',
                    'Excellent for reforestation projects'
                ],
                'medicinal_uses' => [
                    'Bark used for treating skin diseases',
                    'Leaves used for wound healing',
                    'Gum used as traditional medicine',
                    'Root extracts for fever'
                ],
                'cultural_significance' => [
                    'Important for reforestation',
                    'Used in agroforestry systems',
                    'Provides shade for crops',
                    'Source of firewood and charcoal'
                ]
            ],
            [
                'common_name' => 'Molave',
                'scientific_name' => 'Vitex parviflora',
                'description' => 'Molave is a medium to large tree known for its extremely hard and durable wood. It is native to the Philippines and is considered one of the strongest native woods, resistant to termites and weathering.',
                'characteristics' => [
                    'Height: 20-30 meters',
                    'Trunk diameter: 60-100 cm',
                    'Leaves: Compound with 3-5 leaflets',
                    'Flowers: Small, purple',
                    'Fruit: Small black drupe',
                    'Wood: Very hard, termite-resistant'
                ],
                'habitat' => 'Secondary forests, grasslands',
                'conservation_status' => 'Vulnerable',
                'fun_facts' => [
                    'Molave wood is so hard it can dull cutting tools quickly',
                    'Used extensively in traditional Filipino house construction',
                    'Can survive in very dry conditions',
                    'Known as "task force" wood because it was used for military vehicles'
                ],
                'medicinal_uses' => [
                    'Bark used for treating diarrhea',
                    'Leaves used for skin infections',
                    'Root extracts for fever',
                    'Wood ash used in traditional medicine'
                ],
                'cultural_significance' => [
                    'Symbol of strength and resilience',
                    'Used in building traditional houses',
                    'Featured in Filipino proverbs',
                    'Prized for furniture making'
                ]
            ],
            [
                'common_name' => 'Ipil',
                'scientific_name' => 'Intsia bijuga',
                'description' => 'Ipil is a tropical hardwood tree native to Southeast Asia and the Pacific Islands. It produces one of the hardest and most durable woods in the world, highly resistant to termites and marine borers.',
                'characteristics' => [
                    'Height: 30-40 meters',
                    'Trunk diameter: Up to 1.5 meters',
                    'Leaves: Compound with 2-4 leaflets',
                    'Flowers: White to pink',
                    'Fruit: Flat, woody pod',
                    'Wood: Very hard, dark brown'
                ],
                'habitat' => 'Coastal forests, along rivers',
                'conservation_status' => 'Endangered',
                'fun_facts' => [
                    'Ipil wood is so durable it can last for centuries',
                    'Used in traditional boat building',
                    'Wood sinks in water because it is so dense',
                    'Highly resistant to salt water and marine borers'
                ],
                'medicinal_uses' => [
                    'Bark used for treating wounds',
                    'Leaves used for skin diseases',
                    'Root extracts for fever',
                    'Wood shavings used in traditional medicine'
                ],
                'cultural_significance' => [
                    'Used in building traditional boats',
                    'Prized for bridge construction',
                    'Featured in local legends',
                    'Symbol of permanence and durability'
                ]
            ]
        ];

        // Philippine Native Flowers
        $flowers = [
            [
                'common_name' => 'Sampaguita',
                'scientific_name' => 'Jasminum sambac',
                'description' => 'The national flower of the Philippines. Sampaguita is a small, white, star-shaped flower with a sweet, distinctive fragrance that blooms year-round. It is often strung into leis, garlands, and corsages. The flower opens at night and closes in the morning.',
                'characteristics' => [
                    'Flower size: 2-3 cm in diameter',
                    'Petals: 5-9, white',
                    'Bloom time: Year-round',
                    'Fragrance: Sweet and intense, especially at night',
                    'Growth: Twining vine or shrub',
                    'Height: 1-3 meters'
                ],
                'habitat' => 'Tropical and subtropical regions, cultivated in gardens',
                'conservation_status' => 'Common',
                'fun_facts' => [
                    'The flower opens at night and closes in the morning',
                    'Used to make leis and garlands sold on streets',
                    'The name comes from "sumpa kita" (I promise you) in Filipino',
                    'It is the national flower of both the Philippines and Indonesia',
                    'Flowers are often used in religious offerings'
                ],
                'medicinal_uses' => [
                    'Used in aromatherapy for stress relief and relaxation',
                    'Traditional remedy for headaches and fever',
                    'Used in skincare products for its soothing properties',
                    'Flowers brewed as tea for anxiety and insomnia',
                    'Essential oil used in perfumes and massage oils'
                ],
                'cultural_significance' => [
                    'Symbolizes purity, fidelity, and hope',
                    'Used in religious offerings and prayers',
                    'Essential in weddings for garlands',
                    'Welcome garlands for visitors and guests',
                    'Featured in Filipino songs and literature'
                ]
            ],
            [
                'common_name' => 'Sunflower',
                'scientific_name' => 'Helianthus annuus',
                'description' => 'Tall, annual plant with large, daisy-like flower heads. Known for following the sun across the sky (heliotropism) and for its edible seeds. The flower head is actually made up of thousands of tiny flowers.',
                'characteristics' => [
                    'Height: 1.5-3.5 meters',
                    'Flower diameter: 30-50 cm',
                    'Seeds: Edible, oil-rich',
                    'Petals: Yellow, ray florets',
                    'Bloom time: Summer to fall',
                    'Leaves: Large, heart-shaped'
                ],
                'habitat' => 'Open fields, gardens, and farms',
                'conservation_status' => 'Common',
                'fun_facts' => [
                    'Each flower head is made up of thousands of tiny flowers',
                    'Can grow up to 12 feet tall in ideal conditions',
                    'Sunflower seeds are a healthy snack rich in vitamin E',
                    'The sunflower is the only flower with "flower" in its name',
                    'Young sunflowers track the sun across the sky'
                ],
                'medicinal_uses' => [
                    'Seeds are rich in Vitamin E and selenium',
                    'Oil is good for heart health',
                    'Used in traditional medicine for respiratory issues',
                    'Petals used in herbal teas',
                    'Root extracts used for fever'
                ],
                'cultural_significance' => [
                    'Symbol of happiness, optimism, and loyalty',
                    'Often associated with summer',
                    'Used in art and literature',
                    'Popular in photography and paintings',
                    'Represents solar energy and vitality'
                ]
            ],
            [
                'common_name' => 'Rose',
                'scientific_name' => 'Rosa spp.',
                'description' => 'Woody perennial flowering plant with prickly stems and showy flowers. Available in many colors, each with different meanings. Roses are one of the most popular and widely cultivated flowers in the world.',
                'characteristics' => [
                    'Height: 0.5-3 meters',
                    'Flower diameter: 5-15 cm',
                    'Petals: 5-50 depending on variety',
                    'Stems: Prickly',
                    'Leaves: Compound with serrated edges',
                    'Fragrance: Varies by variety'
                ],
                'habitat' => 'Gardens worldwide, temperate regions',
                'conservation_status' => 'Common',
                'fun_facts' => [
                    'There are over 300 species of roses',
                    'Rose fossils date back 35 million years',
                    'The world\'s oldest rose is 1,000 years old',
                    'Roses are related to apples and strawberries',
                    'The rose is the official flower of the United States'
                ],
                'medicinal_uses' => [
                    'Rose water for skincare',
                    'Rich in Vitamin C',
                    'Used in aromatherapy',
                    'Rose hips are high in antioxidants',
                    'Used in traditional medicine for digestion'
                ],
                'cultural_significance' => [
                    'Universal symbol of love and beauty',
                    'Used in perfumes and cosmetics',
                    'Featured in mythology and literature',
                    'Different colors have different meanings',
                    'Used in weddings and celebrations'
                ]
            ],
            [
                'common_name' => 'Orchid',
                'scientific_name' => 'Orchidaceae',
                'description' => 'Diverse family of flowering plants with colorful and often fragrant blooms. Known for their complex and beautiful flowers. Orchids are one of the largest families of flowering plants, with over 25,000 species worldwide.',
                'characteristics' => [
                    'Height: Variable',
                    'Bloom time: Depends on species',
                    'Flowers: Symmetrical, complex',
                    'Roots: Often aerial',
                    'Colors: Wide range including white, pink, purple, yellow'
                ],
                'habitat' => 'Tropical and subtropical regions worldwide',
                'conservation_status' => 'Varies by species',
                'fun_facts' => [
                    'Orchids are one of the largest plant families',
                    'Some orchids can live for 100 years',
                    'Vanilla comes from an orchid',
                    'Orchid seeds are the smallest in the plant kingdom',
                    'Some orchids mimic insects to attract pollinators'
                ],
                'medicinal_uses' => [
                    'Used in traditional Chinese medicine',
                    'Some species used for treating coughs',
                    'Orchid extracts in skincare',
                    'Used in herbal remedies',
                    'Vanilla pods used for flavoring'
                ],
                'cultural_significance' => [
                    'Symbol of luxury, beauty, and strength',
                    'Highly prized in horticulture',
                    'Featured in Asian art and poetry',
                    'Popular in corsages and arrangements',
                    'Represent fertility in some cultures'
                ]
            ],
            [
                'common_name' => 'Gumamela',
                'scientific_name' => 'Hibiscus rosa-sinensis',
                'description' => 'Gumamela is a popular ornamental flower in the Philippines, known for its large, showy blooms. It comes in various colors including red, pink, yellow, and orange. The flower is used in traditional medicine and hair care.',
                'characteristics' => [
                    'Flower diameter: 10-20 cm',
                    'Petals: 5, large and showy',
                    'Height: 2-5 meters as shrub',
                    'Bloom time: Year-round',
                    'Stamen: Long, prominent',
                    'Colors: Red, pink, yellow, orange'
                ],
                'habitat' => 'Tropical gardens, roadsides',
                'conservation_status' => 'Common',
                'fun_facts' => [
                    'Gumamela flowers only last for one day',
                    'Used to shine shoes because of its slippery sap',
                    'The red variety is often used in hair care',
                    'Popular as a natural dye',
                    'Easy to propagate from cuttings'
                ],
                'medicinal_uses' => [
                    'Used for treating cough and colds',
                    'Flowers used for hair growth',
                    'Leaves used for wound healing',
                    'Roots used for fever',
                    'Traditional remedy for hypertension'
                ],
                'cultural_significance' => [
                    'Common in Filipino gardens',
                    'Used in traditional games',
                    'Symbol of beauty and grace',
                    'Featured in folk medicine',
                    'Popular in landscaping'
                ]
            ],
            [
                'common_name' => 'Ylang-Ylang',
                'scientific_name' => 'Cananga odorata',
                'description' => 'Ylang-Ylang is a tropical tree known for its fragrant flowers, which are used in perfumery. The name means "flower of flowers" in Tagalog. It is one of the most important ingredients in the perfume industry.',
                'characteristics' => [
                    'Tree height: 10-30 meters',
                    'Flowers: Yellow, star-shaped',
                    'Fragrance: Strong, sweet, exotic',
                    'Blooms: Year-round',
                    'Petals: 6, long and drooping'
                ],
                'habitat' => 'Tropical lowland forests',
                'conservation_status' => 'Common',
                'fun_facts' => [
                    'Ylang-Ylang means "flower of flowers"',
                    'Essential oil is used in Chanel No. 5',
                    'Flowers are harvested by hand',
                    'The fragrance is stronger at night',
                    'One tree can produce flowers for up to 40 years'
                ],
                'medicinal_uses' => [
                    'Essential oil for relaxation',
                    'Used in aromatherapy for stress',
                    'Traditional remedy for headaches',
                    'Oil used in massage therapy',
                    'Antidepressant properties'
                ],
                'cultural_significance' => [
                    'Used in traditional weddings',
                    'Strewn on beds for fragrance',
                    'Important in perfume industry',
                    'Symbol of romance and sensuality',
                    'Featured in Filipino songs'
                ]
            ],
            [
                'common_name' => 'Dama de Noche',
                'scientific_name' => 'Cestrum nocturnum',
                'description' => 'Dama de Noche, meaning "Lady of the Night," is a shrub known for its intensely fragrant flowers that bloom at night. The small, greenish-white flowers release a powerful, sweet scent after sunset.',
                'characteristics' => [
                    'Height: 2-4 meters',
                    'Flowers: Small, greenish-white',
                    'Bloom time: Night, multiple times a year',
                    'Fragrance: Very strong at night',
                    'Leaves: Simple, elliptical'
                ],
                'habitat' => 'Gardens, roadsides',
                'conservation_status' => 'Common',
                'fun_facts' => [
                    'The fragrance is strongest on moonlit nights',
                    'Flowers attract night-flying moths',
                    'Can be smelled from hundreds of meters away',
                    'The plant is toxic if ingested',
                    'Blooms in cycles throughout the year'
                ],
                'medicinal_uses' => [
                    'Used in traditional medicine with caution',
                    'Leaves used for skin conditions',
                    'Aromatherapy for relaxation',
                    'Traditional remedy for epilepsy',
                    'Used in some herbal preparations'
                ],
                'cultural_significance' => [
                    'Named after women who wear perfume at night',
                    'Featured in romance stories',
                    'Popular in evening gardens',
                    'Symbol of mystery and romance',
                    'Associated with moonlit nights'
                ]
            ]
        ];

        // Insert trees
        foreach ($trees as $tree) {
            Species::create(array_merge($tree, [
                'category_id' => $treeCategory->id,
                'is_active' => true
            ]));
        }

        // Insert flowers
        foreach ($flowers as $flower) {
            Species::create(array_merge($flower, [
                'category_id' => $flowerCategory->id,
                'is_active' => true
            ]));
        }
    }
}
