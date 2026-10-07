<?php
/**
 * defaults.php — default bootstrap settings, ported 1:1 from server.ts
 */

function getDefaultPublicSettings(): array {
    return [
        'platformName' => 'PrecisionMech',
        'favicon' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=32&auto=format&fit=crop&q=80',
        'logo' => '⚙️ PrecisionMech',
        'primaryColor' => '#2563eb',
        'secondaryColor' => '#0f172a',
        'textColor' => '#1e293b',
        'backgroundColor' => '#f8fafc',
        'headingFont' => 'Inter',
        'bodyFont' => 'Inter',
        'footerDescription' => 'Prompt, transparent, and certified appliance repair specialists. Serving households and businesses with absolute craftsmanship.',
        'hero' => [
            'title' => 'Reliable Care for Heavy Machines.',
            'subtitle' => 'Expert diagnostics and preventative maintenance for industrial lathes, CNC machines, and hydraulic systems. Certified by leading manufacturers.',
            'ctaText' => 'Book Now',
            'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800&auto=format&fit=crop&q=80'
        ],
        'services' => [
            ['id' => '1', 'name' => 'CNC Machine Repair', 'description' => 'Fixing spindle errors, axis controller faults, and mechanical wear and tear.', 'icon' => 'Cpu', 'price' => 'From $120', 'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800&auto=format&fit=crop&q=80', 'seoTitle' => 'CNC Machine Repair Service', 'seoDescription' => 'Expert CNC Machine Repair fixing spindle errors and axis controller faults.', 'seoKeywords' => 'cnc repair, spindle error, axis controller'],
            ['id' => '2', 'name' => 'Lathe Diagnostics', 'description' => 'Analyzing runout, gear backlash, headstock issues, and tool turret alignment.', 'icon' => 'Wrench', 'price' => 'From $90', 'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=800&auto=format&fit=crop&q=80', 'seoTitle' => 'Lathe Diagnostics & Repair', 'seoDescription' => 'Professional Lathe Diagnostics analyzing runout and gear backlash.', 'seoKeywords' => 'lathe diagnostics, gear backlash, headstock repair'],
            ['id' => '3', 'name' => 'Hydraulic System Service', 'description' => 'Resolving pump pressure loss, cylinder seal leaks, and control valve issues.', 'icon' => 'Wind', 'price' => 'From $110', 'image' => 'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=800&auto=format&fit=crop&q=80', 'seoTitle' => 'Hydraulic System Service', 'seoDescription' => 'Reliable Hydraulic System Service resolving pump pressure loss and leaks.', 'seoKeywords' => 'hydraulic service, pump pressure, cylinder seal'],
            ['id' => '4', 'name' => 'Part Fabrication', 'description' => 'Precision custom milling for obsolete or hard-to-source machine components.', 'icon' => 'Layers', 'price' => 'From $150', 'image' => 'https://images.unsplash.com/photo-1565439390150-c79402dcbc2f?w=800&auto=format&fit=crop&q=80', 'seoTitle' => 'Custom Part Fabrication', 'seoDescription' => 'Precision custom milling and part fabrication for hard-to-source components.', 'seoKeywords' => 'part fabrication, custom milling, obsolete parts']
        ],
        'machines' => [
            ['id' => '1', 'name' => 'CNC Machining Centers', 'icon' => 'Cpu', 'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800&auto=format&fit=crop&q=80'],
            ['id' => '2', 'name' => 'Manual & CNC Lathes', 'icon' => 'Wrench', 'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=800&auto=format&fit=crop&q=80'],
            ['id' => '3', 'name' => 'Hydraulic Systems', 'icon' => 'Wind', 'image' => 'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=800&auto=format&fit=crop&q=80'],
            ['id' => '4', 'name' => 'Milling Equipment', 'icon' => 'Layers', 'image' => 'https://images.unsplash.com/photo-1565439390150-c79402dcbc2f?w=800&auto=format&fit=crop&q=80'],
            ['id' => '5', 'name' => 'Automation Controllers', 'icon' => 'Cpu', 'image' => 'https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?w=800&auto=format&fit=crop&q=80']
        ],
        'serviceLocations' => [
            ['id' => '1', 'country' => 'United States', 'cities' => ['Mech City', 'San Francisco', 'Austin']]
        ],
        'about' => [
            'title' => 'Precision Engineering Excellence',
            'text' => 'With over 15 years of industrial experience, PrecisionMech is the trusted partner for heavy machinery and shop servicing. We pride ourselves on transparent diagnostic reports, minimum downtime for your facility, and our team of elite, fully certified service technicians. Every service is backed by our solid 90-day parts and labor warranty.',
            'features' => [
                'Same-Day Emergency On-Site Service Available',
                'Fully Certified Heavy Machinery Specialist Technicians',
                'Upfront Diagnostic Audits & Fixed Price Quotes',
                '90-Day Parts & Labor Service Guarantee'
            ],
            'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800&auto=format&fit=crop&q=80'
        ],
        'contact' => [
            'title' => 'Connect with a Specialist',
            'subtitle' => 'Have a critical machine down or need preventative shop maintenance? Get in touch directly or use our automated scheduler.',
            'phone' => '+1 (555) 987-6543',
            'email' => 'service@precisionmech.com',
            'address' => '500 Industrial Parkway, Suite 12, Mech City, MC 94010',
            'hours' => 'Mon - Sat: 8:00 AM - 6:00 PM, Sun: Closed',
            'mapEmbedUrl' => 'https://www.openstreetmap.org/export/embed.html?bbox=-122.4394%2C37.7649%2C-122.4194%2C37.7849&layer=mapnik'
        ],
        'seo' => [
            'home' => ['title' => 'PrecisionMech - Industrial Machine & Heavy Repair Specialist', 'description' => 'Expert diagnostics and preventative maintenance for industrial lathes, CNC machines, and hydraulic systems. Book online in minutes!', 'ogImage' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1200&auto=format&fit=crop&q=80'],
            'about' => ['title' => 'About Us - PrecisionMech', 'description' => 'Learn more about PrecisionMech, our team of expert engineers, and our dedication to premium mechanical servicing.', 'ogImage' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1200&auto=format&fit=crop&q=80'],
            'contact' => ['title' => 'Contact Us - PrecisionMech', 'description' => 'Get in touch with PrecisionMech. View our facility, check our hours, or send us a service request directly.', 'ogImage' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1200&auto=format&fit=crop&q=80'],
            'booking' => ['title' => 'Schedule Service - PrecisionMech', 'description' => 'Book your technician service appointment online. Select your service area and choose a date.', 'ogImage' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=1200&auto=format&fit=crop&q=80']
        ]
    ];
}

function getDefaultSecretSettings(): array {
    return [
        'waGatewayUrl' => 'http://13.60.229.151:3000/api/messages/send',
        'waApiKey' => '',
        'waReceiverNumber' => '',
        'smtpHost' => '',
        'smtpPort' => '587',
        'smtpUser' => '',
        'smtpPass' => '',
        'emailTemplate' => "<h3>Booking Confirmation</h3>\n<p>Hi {{name}},</p>\n<p>Thank you for choosing <strong>{{platformName}}</strong>! Your booking has been successfully received.</p>\n<p><strong>Appointment Details:</strong></p>\n<ul>\n  <li><strong>Machine:</strong> {{machine}}</li>\n  <li><strong>Type:</strong> {{symptom}}</li>\n  <li><strong>Location:</strong> {{location}}</li>\n  <li><strong>Date & Time Slot:</strong> {{datetime}}</li>\n  <li><strong>Status:</strong> Scheduled</li>\n</ul>\n<p>Our technician will contact you shortly at {{phone}}.</p>\n<p>Best regards,<br/>The {{platformName}} Team</p>"
    ];
}

/**
 * Bootstrap settings into SQLite on first run - mirrors the startup check
 * in server.ts.
 */
function bootstrapSettings(): void {
    if (getSetting('public') === null) {
        setSetting('public', getDefaultPublicSettings());
    }
    if (getSetting('secrets') === null) {
        setSetting('secrets', getDefaultSecretSettings());
    }
    bootstrapAdminUsers();
}

/**
 * Ensure at least one admin account exists (username + password in MySQL).
 */
function bootstrapAdminUsers(): void {
    if (countAdminUsers() > 0) {
        return;
    }
    createAdminUser('admin', 'admin123');
}
