<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Legal Pages Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for legal pages (Imprint, Privacy Policy, Terms of Service).
    | Texts can be customized here. Use placeholders like {{company_name}} for dynamic content.
    |
    */

    'company' => [
        'name' => env('LEGAL_COMPANY_NAME', 'Your Company Name'),
        'address' => env('LEGAL_COMPANY_ADDRESS', 'Your Company Address'),
        'city' => env('LEGAL_COMPANY_CITY', 'Your City'),
        'country' => env('LEGAL_COMPANY_COUNTRY', 'Your Country'),
        'email' => env('LEGAL_COMPANY_EMAIL', 'contact@yourcompany.com'),
        'phone' => env('LEGAL_COMPANY_PHONE', '+49 (0) 123 4567890'),
        'website' => env('LEGAL_COMPANY_WEBSITE', env('APP_URL', 'https://yourcompany.com')),
    ],

    'responsible_person' => [
        'name' => env('LEGAL_RESPONSIBLE_NAME', 'John Doe'),
        'title' => env('LEGAL_RESPONSIBLE_TITLE', 'CEO'),
        'email' => env('LEGAL_RESPONSIBLE_EMAIL', 'ceo@yourcompany.com'),
    ],

    'data_protection_officer' => [
        'name' => env('LEGAL_DPO_NAME', ''),
        'email' => env('LEGAL_DPO_EMAIL', ''),
    ],

    'imprint' => [
        'title' => 'Imprint',
        'content' => '
            <h2>Information according to § 5 DDG (Digital Services Act)</h2>
            <p>{{company_name}}<br>
            {{company_address}}<br>
            {{company_city}}, {{company_country}}</p>

            <h2>Contact</h2>
            <p><strong>Email:</strong> {{company_email}}<br>
            <strong>Phone:</strong> {{company_phone}}<br>
            <strong>Website:</strong> <a href="{{company_website}}">{{company_website}}</a></p>

            <h2>Responsible for Content</h2>
            <p>{{responsible_name}}<br>
            {{responsible_title}}<br>
            <strong>Email:</strong> {{responsible_email}}</p>

            <h2>About This Service</h2>
            <p>This platform allows users to share and discover Twitch clips from their favorite streamers and games. We provide a community-driven space for gaming content enthusiasts.</p>

            <h2>Disclaimer</h2>
            <p>All content published on this platform has been created with utmost care. However, we cannot guarantee the accuracy, completeness, or timeliness of user-submitted content.</p>

            <p>As a service provider, we are responsible for our own content according to § 7 DDG. According to §§ 8 to 10 DDG, we are not obligated to monitor transmitted or stored third-party information or investigate circumstances indicating illegal activity.</p>

            <p>Our obligations to remove or block information under general laws remain unaffected. Liability in this regard is only possible from the point in time at which we become aware of a specific infringement. Upon notification of such violations, we will remove the content immediately.</p>

            <h2>External Links</h2>
            <p>This platform contains links to external websites (including Twitch.tv) over whose content we have no control. Therefore, we cannot accept any liability for this external content. The respective provider or operator of the linked pages is always responsible for their content.</p>
        ',
    ],

    'privacy_policy' => [
        'title' => 'Privacy Policy',
        'content' => '
            <h2>1. Data Protection at a Glance</h2>
            <h3>General Information</h3>
            <p>The following information provides a simple overview of what happens to your personal data when you use our Twitch clips sharing platform. Personal data is any data that can be used to personally identify you.</p>

            <h3>Data Collection on Our Website</h3>
            <p><strong>Who is responsible for data collection on this website?</strong><br>
            Data processing on this website is carried out by the website operator. You can find their contact details in the imprint of this website.</p>

            <h3>How do we collect your data?</h3>
            <p>Your data is collected primarily through Twitch OAuth authentication when you log in. We receive your Twitch username, user ID, and profile information.</p>
            <p>When you submit clips, we store information about the Twitch clips you share, including clip IDs and metadata.</p>
            <p>Technical data is automatically collected by our IT systems (e.g., browser type, operating system, access times) to ensure proper platform functionality.</p>

            <h3>What do we use your data for?</h3>
            <p>We use your data to provide platform functionality, including user authentication, clip submissions, and displaying your profile. Technical data helps us maintain platform security and performance.</p>

            <h3>What rights do you have regarding your data?</h3>
            <p>You have the right to receive information about the origin, recipient and purpose of your stored personal data free of charge at any time. You also have the right to request the correction, blocking or deletion of this data. For this purpose, as well as for further questions on the subject of data protection, you can contact us at any time at the address given in the imprint. Furthermore, you have the right to lodge a complaint with the competent supervisory authority.</p>

            <h2>2. General Information and Mandatory Information</h2>
            <h3>Data Protection</h3>
            <p>The operators of these pages take the protection of your personal data very seriously. We treat your personal data confidentially and in accordance with the statutory data protection regulations and this privacy policy.</p>
            <p>When you use this website, various personal data is collected. Personal data is data that can be used to personally identify you. This privacy policy explains what data we collect and what we use it for. It also explains how and for what purpose this is done.</p>
            <p>Please note that data transmission over the Internet (e.g., when communicating by email) may have security gaps. Complete protection of data against access by third parties is not possible.</p>

            <h3>Notice Concerning the Responsible Party</h3>
            <p>The responsible party for data processing on this website is:</p>
            <p>{{company_name}}<br>
            {{company_address}}<br>
            {{company_city}}, {{company_country}}<br>
            Email: {{company_email}}</p>

            <p>The responsible party is the natural or legal person who alone or jointly with others decides on the purposes and means of processing personal data (e.g., names, email addresses, etc.).</p>

            <h2>3. Data Collection on Our Website</h2>
            <h3>Cookies</h3>
            <p>Our website uses cookies. Cookies are small text files that are stored on your computer and saved by your browser. Cookies do not harm your computer and do not contain viruses.</p>
            <p>Cookies help us to make our website more user-friendly, effective and secure. Most of the cookies we use are "session cookies". They are automatically deleted at the end of your visit.</p>
            <p>You can set your browser so that you are informed about the setting of cookies and only allow cookies in individual cases, exclude the acceptance of cookies for certain cases or in general, and activate the automatic deletion of cookies when closing the browser. If cookies are deactivated, the functionality of this website may be limited.</p>

            <h3>Twitch OAuth Authentication</h3>
            <p>This platform uses Twitch OAuth for user authentication. When you log in, Twitch provides us with your:</p>
            <ul>
                <li>Twitch username and display name</li>
                <li>Twitch user ID</li>
                <li>Profile picture URL</li>
                <li>Email address (if authorized)</li>
            </ul>
            <p>This data is used solely for authentication and providing platform features. We do not share this data with third parties.</p>

            <h3>Embedded Twitch Content</h3>
            <p>When viewing clips on our platform, we embed Twitch player iframes. Loading these players may transmit data to Twitch. We use a privacy-first approach where clips are loaded only after user consent. Please refer to <a href="https://www.twitch.tv/p/legal/privacy-notice/" target="_blank" rel="noopener noreferrer">Twitch\'s Privacy Policy</a> for more information.</p>

            <h3>Server Log Files</h3>
            <p>Our server automatically collects and stores technical information in log files:</p>
            <ul>
                <li>Browser type and version</li>
                <li>Operating system</li>
                <li>Referrer URL</li>
                <li>IP address (anonymized)</li>
                <li>Access time</li>
            </ul>
            <p>This data cannot be assigned to specific persons and is used solely for security and performance monitoring.</p>

            <h2>4. Data Protection Officer</h2>
            <p>We have appointed a data protection officer for our company.</p>
            <p>{{dpo_name}}<br>
            Email: {{dpo_email}}</p>
        ',
    ],

    'terms_of_service' => [
        'title' => 'Terms of Service',
        'content' => '
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing and using {{company_name}} (the "Service"), you accept and agree to be bound by these Terms of Service. This platform is designed for sharing and discovering Twitch clips.</p>

            <h2>2. User Accounts</h2>
            <p>To submit clips, you must authenticate using your Twitch account. You are responsible for:</p>
            <ul>
                <li>Maintaining the security of your account</li>
                <li>All activities that occur under your account</li>
                <li>Ensuring your submissions comply with these terms</li>
            </ul>

            <h2>3. Content Submission</h2>
            <p>When submitting Twitch clips to our platform, you agree that:</p>
            <ul>
                <li>You only submit clips you have the right to share</li>
                <li>Clips must comply with Twitch\'s Terms of Service</li>
                <li>Content must not violate any laws or third-party rights</li>
                <li>You will not submit spam, offensive, or illegal content</li>
                <li>We reserve the right to remove any content at our discretion</li>
            </ul>

            <h2>4. Intellectual Property</h2>
            <p>All Twitch content, including clips, remains the property of Twitch Interactive, Inc. and the respective content creators. This platform merely provides links to and embeds of publicly available Twitch content.</p>
            <p>Our platform\'s original code, design, and features are protected by copyright and other intellectual property laws.</p>

            <h2>5. Third-Party Services</h2>
            <p>This platform integrates with Twitch services. Your use of Twitch content is subject to <a href="https://www.twitch.tv/p/legal/terms-of-service/" target="_blank" rel="noopener noreferrer">Twitch\'s Terms of Service</a>.</p>
            <p>We are not responsible for:</p>
            <ul>
                <li>Availability or functionality of Twitch services</li>
                <li>Content created by Twitch streamers</li>
                <li>Changes to Twitch\'s API or embed functionality</li>
            </ul>

            <h2>6. Prohibited Conduct</h2>
            <p>You may not:</p>
            <ul>
                <li>Submit malicious, offensive, or illegal content</li>
                <li>Attempt to bypass or manipulate our systems</li>
                <li>Harass, abuse, or harm other users</li>
                <li>Use automated tools to spam submissions</li>
                <li>Violate any applicable laws or regulations</li>
            </ul>

            <h2>7. Disclaimer</h2>
            <p>This platform is provided "as is" without warranties of any kind. We do not guarantee uninterrupted access, accuracy of user-submitted content, or availability of embedded Twitch clips.</p>

            <h2>8. Content Moderation</h2>
            <p>We reserve the right to review, moderate, and remove any submitted clips that violate these terms or community standards. Repeated violations may result in account suspension.</p>

            <h2>9. Changes to Terms</h2>
            <p>We may update these Terms of Service from time to time. Continued use of the platform after changes constitutes acceptance of the updated terms. Significant changes will be communicated through the platform.</p>

            <h2>10. Governing Law</h2>
            <p>These terms are governed by the laws of {{company_country}}. Any disputes shall be resolved in the courts of {{company_country}}.</p>

            <h2>11. Contact</h2>
            <p>For questions about these Terms of Service:</p>
            <p><strong>{{company_name}}</strong><br>
            <strong>Email:</strong> {{company_email}}<br>
            <strong>Website:</strong> {{company_website}}</p>
        ',
    ],
];