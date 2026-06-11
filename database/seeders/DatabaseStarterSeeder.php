<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseStarterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to avoid errors
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tables to avoid duplicates
        DB::table('users')->truncate();
        DB::table('properties')->truncate();
        DB::table('property_gallery')->truncate();
        DB::table('conversations')->truncate();
        DB::table('messages')->truncate();
        DB::table('subscriptions')->truncate();
        DB::table('support_tickets')->truncate();
        DB::table('ticket_replies')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // ========== CREATE ADMIN USERS ==========
        $adminId = $this->createAdmin();
        
        // ========== CREATE LANDLORD USERS ==========
        $landlordIds = $this->createLandlords();
        
        // ========== CREATE TENANT USERS ==========
        $tenantIds = $this->createTenants();
        
        // ========== CREATE SUBSCRIPTIONS ==========
        $this->createSubscriptions($landlordIds);
        
        // ========== CREATE SUPPORT TICKETS ==========
        $this->createSupportTickets($tenantIds, $landlordIds, $adminId);
        
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('📊 Created: 1 Admin, ' . count($landlordIds) . ' Landlords, ' . count($tenantIds) . ' Tenants');
    }
    
    /**
     * Create Admin User
     */
    private function createAdmin()
    {
        $admin = [
            'name' => 'Super Admin',
            'email' => 'admin@nestly.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'phone' => '+254700000001',
            'role' => 'admin',
            'subscription_plan' => 'platinum',
            'is_verified' => true,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $id = DB::table('users')->insertGetId($admin);
        $this->command->info('👑 Admin created: admin@nestly.com (password: password123)');
        
        return $id;
    }
    
    /**
     * Create Landlord Users
     */
    private function createLandlords()
    {
        $landlords = [
            [
                'name' => 'James Mwangi',
                'email' => 'james@nestly.com',
                'phone' => '+254712345678',
                'company' => 'Mwangi Properties Ltd',
                'location' => 'Nairobi, Kilimani',
            ],
            [
                'name' => 'Grace Achieng',
                'email' => 'grace@nestly.com',
                'phone' => '+254723456789',
                'company' => 'Achieng Real Estate',
                'location' => 'Nairobi, Westlands',
            ],
            [
                'name' => 'Peter Omondi',
                'email' => 'peter@nestly.com',
                'phone' => '+254734567890',
                'company' => 'Omondi Homes',
                'location' => 'Mombasa, Nyali',
            ],
            [
                'name' => 'Fatma Hassan',
                'email' => 'fatma@nestly.com',
                'phone' => '+254745678901',
                'company' => 'Hassan Investments',
                'location' => 'Kisumu, Milimani',
            ],
            [
                'name' => 'John Kariuki',
                'email' => 'john@nestly.com',
                'phone' => '+254756789012',
                'company' => 'Kariuki Properties',
                'location' => 'Nairobi, Karen',
            ],
            [
                'name' => 'Lucy Wanjiku',
                'email' => 'lucy@nestly.com',
                'phone' => '+254767890123',
                'company' => 'Wanjiku Realty',
                'location' => 'Kiambu, Thika Road',
            ],
        ];
        
        $landlordIds = [];
        
        foreach ($landlords as $index => $landlord) {
            $id = DB::table('users')->insertGetId([
                'name' => $landlord['name'],
                'email' => $landlord['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'phone' => $landlord['phone'],
                'role' => 'landlord',
                'subscription_plan' => 'standard',
                'is_verified' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $landlordIds[] = $id;
            
            // Create a property for each landlord
            $this->createProperty($id, $landlord, $index);
            
            $this->command->info("🏠 Landlord created: {$landlord['email']} (password: password123)");
        }
        
        return $landlordIds;
    }
    
    /**
     * Create Tenant Users
     */
    private function createTenants()
    {
        $tenants = [
            ['name' => 'Alex Okoth', 'email' => 'alex@nestly.com', 'phone' => '+254712345679', 'location' => 'Nairobi'],
            ['name' => 'Sarah Kimani', 'email' => 'sarah@nestly.com', 'phone' => '+254723456780', 'location' => 'Nairobi'],
            ['name' => 'Michael Otieno', 'email' => 'michael@nestly.com', 'phone' => '+254734567891', 'location' => 'Mombasa'],
            ['name' => 'Esther Nduta', 'email' => 'esther@nestly.com', 'phone' => '+254745678902', 'location' => 'Kisumu'],
            ['name' => 'David Mwangi', 'email' => 'david@nestly.com', 'phone' => '+254756789013', 'location' => 'Nakuru'],
            ['name' => 'Catherine Wambui', 'email' => 'catherine@nestly.com', 'phone' => '+254767890124', 'location' => 'Eldoret'],
            ['name' => 'Brian Kipchoge', 'email' => 'brian@nestly.com', 'phone' => '+254778901235', 'location' => 'Nairobi'],
            ['name' => 'Mercy Atieno', 'email' => 'mercy@nestly.com', 'phone' => '+254789012346', 'location' => 'Mombasa'],
        ];
        
        $tenantIds = [];
        
        foreach ($tenants as $tenant) {
            $id = DB::table('users')->insertGetId([
                'name' => $tenant['name'],
                'email' => $tenant['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'phone' => $tenant['phone'],
                'role' => 'tenant',
                'subscription_plan' => 'free',
                'is_verified' => true,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $tenantIds[] = $id;
            $this->command->info("👤 Tenant created: {$tenant['email']} (password: password123)");
        }
        
        return $tenantIds;
    }
    
    /**
     * Create Property for Landlord
     */
    private function createProperty($userId, $landlord, $index)
    {
        $properties = [
            [
                'title' => 'Modern 3-Bedroom Apartment in Kilimani',
                'description' => 'Beautiful modern apartment with stunning city views. Features include: fully fitted kitchen, spacious living room, 24/7 security, parking, and backup water. Walking distance to Junction Mall.',
                'location' => 'Nairobi, Kilimani',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area_sqft' => 1450,
                'price' => 18500000,
                'price_period' => 'sale',
                'main_image' => 'https://images.pexels.com/photos/106399/pexels-photo-106399.jpeg',
                'status' => 'active',
                'is_featured' => $index === 0,
                'is_verified' => true,
            ],
            [
                'title' => 'Spacious 4-Bedroom Villa in Karen',
                'description' => 'Luxury villa in a gated community. Features: swimming pool, large garden, servant quarters, double parking, solar panels, and backup generator. Perfect for families.',
                'location' => 'Nairobi, Karen',
                'bedrooms' => 4,
                'bathrooms' => 3,
                'area_sqft' => 3200,
                'price' => 45000000,
                'price_period' => 'sale',
                'main_image' => 'https://images.pexels.com/photos/2587054/pexels-photo-2587054.jpeg',
                'status' => 'active',
                'is_featured' => $index === 1,
                'is_verified' => true,
            ],
            [
                'title' => 'Oceanfront 2-Bedroom Apartment in Nyali',
                'description' => 'Beachfront apartment with breathtaking ocean views. Walking distance to Nyali Beach. Includes: furnished unit, swimming pool, gym, 24/7 security, and backup power.',
                'location' => 'Mombasa, Nyali',
                'bedrooms' => 2,
                'bathrooms' => 2,
                'area_sqft' => 1200,
                'price' => 25000000,
                'price_period' => 'sale',
                'main_image' => 'https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg',
                'status' => 'active',
                'is_featured' => $index === 2,
                'is_verified' => true,
            ],
            [
                'title' => 'Cozy 3-Bedroom Townhouse in Westlands',
                'description' => 'Modern townhouse in prime location. Features: open plan living, private garden, two parking spaces, and close to Sarit Centre. Ideal for young professionals.',
                'location' => 'Nairobi, Westlands',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area_sqft' => 1800,
                'price' => 12500000,
                'price_period' => 'sale',
                'main_image' => 'https://images.pexels.com/photos/276724/pexels-photo-276724.jpeg',
                'status' => 'active',
                'is_featured' => false,
                'is_verified' => true,
            ],
            [
                'title' => 'Lakeside Studio in Milimani Kisumu',
                'description' => 'Cozy studio apartment near Lake Victoria. Perfect for singles or couples. Features: modern finishes, balcony with lake view, secure compound.',
                'location' => 'Kisumu, Milimani',
                'bedrooms' => 0,
                'bathrooms' => 1,
                'area_sqft' => 550,
                'price' => 8500000,
                'price_period' => 'sale',
                'main_image' => 'https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg',
                'status' => 'active',
                'is_featured' => false,
                'is_verified' => true,
            ],
            [
                'title' => 'Executive Penthouse in Runda',
                'description' => 'Luxury penthouse with panoramic views. Features: 4 bedrooms, 3.5 bathrooms, private elevator, rooftop terrace, gym, and swimming pool.',
                'location' => 'Nairobi, Runda',
                'bedrooms' => 4,
                'bathrooms' => 3,
                'area_sqft' => 2800,
                'price' => 68000000,
                'price_period' => 'sale',
                'main_image' => 'https://images.pexels.com/photos/280229/pexels-photo-280229.jpeg',
                'status' => 'active',
                'is_featured' => true,
                'is_verified' => true,
            ],
        ];
        
        // Use index to cycle through properties
        $propertyData = $properties[$index % count($properties)];
        
        $propertyId = DB::table('properties')->insertGetId([
            'user_id' => $userId,
            'title' => $propertyData['title'],
            'description' => $propertyData['description'],
            'location' => $propertyData['location'],
            'bedrooms' => $propertyData['bedrooms'],
            'bathrooms' => $propertyData['bathrooms'],
            'area_sqft' => $propertyData['area_sqft'],
            'price' => $propertyData['price'],
            'price_period' => $propertyData['price_period'],
            'main_image' => $propertyData['main_image'],
            'status' => $propertyData['status'],
            'is_featured' => $propertyData['is_featured'],
            'is_verified' => $propertyData['is_verified'],
            'views_count' => rand(50, 500),
            'inquiry_count' => rand(5, 50),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Add gallery images for the property
        $galleryImages = [
            'https://images.pexels.com/photos/106399/pexels-photo-106399.jpeg',
            'https://images.pexels.com/photos/2587054/pexels-photo-2587054.jpeg',
            'https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg',
            'https://images.pexels.com/photos/276724/pexels-photo-276724.jpeg',
            'https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg',
        ];
        
        foreach ($galleryImages as $order => $imageUrl) {
            DB::table('property_gallery')->insert([
                'property_id' => $propertyId,
                'image_url' => $imageUrl,
                'type' => 'image',
                'order' => $order,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    /**
     * Create Subscriptions for Landlords
     */
    private function createSubscriptions($landlordIds)
    {
        $plans = ['standard', 'gold', 'platinum'];
        $prices = ['standard' => 499, 'gold' => 999, 'platinum' => 1999];
        
        foreach ($landlordIds as $index => $landlordId) {
            // Assign different plans to different landlords
            $plan = $plans[$index % count($plans)];
            
            DB::table('subscriptions')->insert([
                'user_id' => $landlordId,
                'plan' => $plan,
                'amount' => $prices[$plan],
                'payment_method' => 'mpesa',
                'transaction_id' => 'TXN' . strtoupper(uniqid()),
                'starts_at' => now(),
                'expires_at' => now()->addMonths(rand(1, 12)),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info('💰 Subscriptions created for landlords');
    }
    
    /**
     * Create Support Tickets
     */
    private function createSupportTickets($tenantIds, $landlordIds, $adminId)
    {
        $ticketSubjects = [
            'Cannot upload property images',
            'Payment not reflecting',
            'Account verification issue',
            'How to list multiple properties?',
            'Chat messages not sending',
            'Wrong property location displayed',
        ];
        
        $ticketMessages = [
            'I have been trying to upload images for my property but it keeps failing.',
            'I made payment via M-Pesa but my subscription is still showing as free.',
            'Please verify my landlord account, I have uploaded my documents.',
            'Is there a way to list multiple properties under one account?',
            'My messages to tenants are not going through, please help.',
            'The location pin on my property is showing the wrong area.',
        ];
        
        // Create tickets from tenants
        for ($i = 0; $i < 5; $i++) {
            $ticketId = DB::table('support_tickets')->insertGetId([
                'user_id' => $tenantIds[$i % count($tenantIds)],
                'ticket_id' => 'TKT-' . strtoupper(uniqid()),
                'subject' => $ticketSubjects[$i % count($ticketSubjects)],
                'category' => 'technical',
                'priority' => 'medium',
                'message' => $ticketMessages[$i % count($ticketMessages)],
                'status' => 'open',
                'created_at' => now()->subDays(rand(1, 10)),
                'updated_at' => now(),
            ]);
            
            // Add admin reply to some tickets
            if ($i % 2 == 0) {
                DB::table('ticket_replies')->insert([
                    'ticket_id' => $ticketId,
                    'user_id' => $adminId,
                    'message' => 'Thank you for reaching out. Our team is looking into this issue and will get back to you shortly.',
                    'is_admin_reply' => true,
                    'created_at' => now()->subDays(rand(1, 5)),
                    'updated_at' => now(),
                ]);
                
                DB::table('support_tickets')
                    ->where('id', $ticketId)
                    ->update(['status' => 'in_progress']);
            }
        }
        
        // Create tickets from landlords
        for ($i = 0; $i < 3; $i++) {
            DB::table('support_tickets')->insert([
                'user_id' => $landlordIds[$i % count($landlordIds)],
                'ticket_id' => 'TKT-' . strtoupper(uniqid()),
                'subject' => 'Billing inquiry - Subscription upgrade',
                'category' => 'billing',
                'priority' => 'high',
                'message' => 'I want to upgrade my plan but the payment is failing. Please assist.',
                'status' => 'open',
                'created_at' => now()->subDays(rand(1, 7)),
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info('🎫 Support tickets created');
    }
}