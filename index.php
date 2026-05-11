<?php
session_start();

// Language selection
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'si');
$_SESSION['lang'] = $lang;

$translations = [
    'si' => [
        'title' => 'වයඹ පළාත් ඩිජිටල් බිහිදොර',
        'welcome' => 'වයඹ පළාත් සභාව වෙත සාදරයෙන් පිළිගනිමු',
        'subtitle' => 'ශ්‍රී ලංකාවේ වයඹ පළාත් සභාවේ ප්‍රධානම ඩිජිටල් බිහිදොර',
        'search_placeholder' => 'ආයතනය, දෙපාර්තමේන්තුව හෝ සේවාව සොයන්න...',
        'cat_a' => 'ප්‍රධාන ආයතන',
        'cat_b' => 'පළාත් අමාත්‍යංශ',
        'cat_c' => 'පළාත් දෙපාර්තමේන්තු',
        'cat_d' => 'අනෙකුත් ආයතන',
        'cat_e' => 'ව්‍යවස්ථාපිත මණ්ඩල',
        'cat_f' => 'පොදු මාර්ගගත වෙබ් පද්ධති',
        'visit' => 'පිවිසෙන්න',
        'bot_title' => 'NWPC AI සහායක',
        'bot_msg' => 'ආයුබෝවන්! මම NWPC ඩිජිටල් සහායක. ඔබට උදව් කරන්නේ කෙසේද?',
        'ask_secretary' => 'ප්‍රධාන ලේකම්ගෙන් අහන්න',
        'address' => 'වයඹ පළාත් සභා කාර්යාල සංකීරණය, කුරුණෑගල',
        'footer_text' => 'ඩිජිටල් අංශය - වයඹ පළාත් සභාව මගින් බලගන්වන ලදී',
        'gov_links' => 'ප්‍රධාන රාජ්‍ය ආයතන',
        'important_links' => 'වැදගත් සබැඳි',
        'presidency' => 'ජනාධිපති ලේකම් කාර්යාලය',
        'pm_office' => 'ආග්‍රාමාත්‍ය කාර්යාලය',
        'pc_min' => 'පළාත් සභා හා පළාත් පාලන අමාත්‍යංශය',
        'pub_ad' => 'රාජ්‍ය පරිපාලන අමාත්‍යංශය',
        'finance' => 'මුදල් අමාත්‍යංශය',
        'gic' => 'රාජ්‍ය තොරතුරු කේන්ද්‍රය (1919)',
    ],
    'en' => [
        'title' => 'NWPC Digital Portal',
        'welcome' => 'Welcome to North Western Province',
        'subtitle' => 'The Primary Digital Gateway of North Western Provincial Council',
        'search_placeholder' => 'Search for Agency, Department or Service...',
        'cat_a' => 'Main Institutions',
        'cat_b' => 'Provincial Ministries',
        'cat_c' => 'Provincial Departments',
        'cat_d' => 'Other Institutions',
        'cat_e' => 'Statutory Boards',
        'cat_f' => 'Public Online Systems',
        'visit' => 'Visit Site',
        'bot_title' => 'NWPC AI Assistant',
        'bot_msg' => 'Hello! I am NWPC Digital Assistant. How can I help you?',
        'ask_secretary' => 'Ask Chief Secretary',
        'address' => 'Provincial Council Complex, Kurunegala',
        'footer_text' => 'Powered by Digital Division - NWPC',
        'gov_links' => 'Main Government Agencies',
        'important_links' => 'Important Links',
        'presidency' => 'Presidential Secretariat',
        'pm_office' => 'Prime Minister\'s Office',
        'pc_min' => 'Ministry of Provincial Councils',
        'pub_ad' => 'Ministry of Public Administration',
        'finance' => 'Ministry of Finance',
        'gic' => 'Government Information Center (1919)',
    ],
    'ta' => [
        'title' => 'NWPC டிஜிட்டல் போர்டல்',
        'welcome' => 'வடமேல் மாகாண சபைக்கு உங்களை வரவேற்கிறோம்',
        'subtitle' => 'வடமேல் மாகாண சபையின் பிரதான டிஜிட்டல் நுழைவாயில்',
        'search_placeholder' => 'நிறுவனம் அல்லது சேவையைத் தேடுங்கள்...',
        'cat_a' => 'முக்கிய நிறுவனங்கள்',
        'cat_b' => 'மாகாண அமைச்சுகள்',
        'cat_c' => 'மாகாண திணைக்களங்கள்',
        'cat_d' => 'ஏனைய நிறுவனங்கள்',
        'cat_e' => 'சட்டரீதியான சபைகள்',
        'cat_f' => 'பொதுவான ஆன்லைன் அமைப்புகள்',
        'visit' => 'விஜயம் செய்யுங்கள்',
        'bot_title' => 'NWPC AI உதவியாளர்',
        'bot_msg' => 'வணக்கம்! நான் NWPC டிஜிட்டல் உதவியாளர். உங்களுக்கு நான் எவ்வாறு உதவ முடியும்?',
        'ask_secretary' => 'தலைமைச் செயலாளரிடம் கேளுங்கள்',
        'address' => 'மாகாண சபை வளாகம், குருணாகல்',
        'footer_text' => 'டிஜிட்டல் பிரிவினால் இயக்கப்படுகிறது - NWPC',
        'gov_links' => 'முக்கிய அரசாங்க முகவர் நிலையங்கள்',
        'important_links' => 'முக்கிய இணைப்புகள்',
        'presidency' => 'ஜனாதிபதி செயலகம்',
        'pm_office' => 'பிரதமர் அலுவலகம்',
        'pc_min' => 'மாகாண சபைகள் அமைச்சு',
        'pub_ad' => 'ராஜ்ய பரிபாலன அமைச்சு',
        'finance' => 'நிதி அமைச்சு',
        'gic' => 'அரசாங்க தகவல் மையம் (1919)',
    ]
];

$t = $translations[$lang];

// Include Knowledge Base
include 'chatbot-knowledge.php';

$jsonKnowledge = json_encode($knowledgeBase, JSON_UNESCAPED_UNICODE);

$links = [
    'A' => [
        ['name' => ['si' => 'ආණ්ඩුකාරවර කාර්යාලය', 'en' => 'Governor\'s Office', 'ta' => 'ஆளுநர் அலுவலகம்'], 'url' => 'https://governor.nw.gov.lk'],
        ['name' => ['si' => 'පළාත් සභා ලේකම් කාර්යාලය', 'en' => 'PC Secretariat', 'ta' => 'மாகாண சபை செயலகம்'], 'url' => 'https://pcsec.nw.gov.lk'],
        ['name' => ['si' => 'රාජ්‍ය සේවා කොමිෂන් සභාව', 'en' => 'Public Service Commission', 'ta' => 'அரச சேவை ஆணைக்குழு'], 'url' => 'https://psc.nw.gov.lk'],
        ['name' => ['si' => 'ප්‍රධාන ලේකම් කාර්යාලය', 'en' => 'Chief Secretary\'s Office', 'ta' => 'பிரதம செயலாளர் அலுவலகம்'], 'url' => 'https://cs.nw.gov.lk'],
        ['name' => ['si' => 'පළාත් භාණ්ඩාගාරය', 'en' => 'Provincial Treasury', 'ta' => 'மாகாண திறைசேரி'], 'url' => 'https://treasury.nw.gov.lk'],
        ['name' => ['si' => 'පුහුණු අංශය (MDTU)', 'en' => 'Training Division', 'ta' => 'பயிற்சிப் பிரிவு'], 'url' => 'https://mdtu.nw.gov.lk'],
    ],
    'B' => [
        ['name' => ['si' => 'වයඹ ප්‍රධාන අමාත්‍යංශය', 'en' => 'Chief Ministry', 'ta' => 'பிரதான அமைச்சு'], 'url' => 'https://www.cm.nw.gov.lk'],
        ['name' => ['si' => 'පළාත් සෞඛ්‍ය අමාත්‍යංශය', 'en' => 'Ministry of Health', 'ta' => 'சுகாதார அமைச்சு'], 'url' => 'https://healthmin.nw.gov.lk'],
        ['name' => ['si' => 'පළාත් මාර්ග අමාත්‍යංශය', 'en' => 'Ministry of Roads', 'ta' => 'வீதி அமைச்சு'], 'url' => 'https://roadmin.nw.gov.lk'],
        ['name' => ['si' => 'පළාත් සමූපකාර අමාත්‍යංශය', 'en' => 'Ministry of Co-operatives', 'ta' => 'கூட்டுறவு அமைச்சு'], 'url' => 'https://moe.nw.gov.lk'],
        ['name' => ['si' => 'පළාත් කෘෂිකර්ම ආමත්‍යංශය', 'en' => 'Ministry of Agriculture', 'ta' => 'விவசாய அமைச்சு'], 'url' => 'https://agrimin.nw.gov.lk'],
    ],
    'C' => [
        ['name' => ['si' => 'කෘෂිකර්ම දෙපාර්තමේන්තුව', 'en' => 'Dept. of Agriculture', 'ta' => 'விவசாய திணைக்களம்'], 'url' => 'https://agridep-wp-north-western-province-council-production-3.apps.red-k8s.akaza.lk'],
        ['name' => ['si' => 'සත්ව නිෂ්පාදන හා සෞඛ්‍ය දෙපාර්තමේන්තුව', 'en' => 'Dept. of Animal Production', 'ta' => 'கால்நடை உற்பத்தி திணைக்களம்'], 'url' => 'https://aphdept.nw.gov.lk'],
        ['name' => ['si' => 'ආයුර්වේද දෙපාර්තමේන්තුව', 'en' => 'Dept. of Ayurveda', 'ta' => 'ஆயுர்வேத திணைக்களம்'], 'url' => 'https://ayurdept.nw.gov.lk'],
        ['name' => ['si' => 'සමූපකාර සංවර්ධන දෙපාර්තමේන්තුව', 'en' => 'Dept. of Co-operative Development', 'ta' => 'கூட்டுறவு அபிவிருத்தி திணைக்களம்'], 'url' => 'https://coopnw.lk'],
        ['name' => ['si' => 'අධ්‍යාපන දෙපාර්තමේන්තුව', 'en' => 'Dept. of Education', 'ta' => 'கல்வித் திணைக்களம்'], 'url' => 'https://nwpedu.lk'],
        ['name' => ['si' => 'ඉංජිනේරු දෙපාර්තමේන්තුව', 'en' => 'Dept. of Engineering', 'ta' => 'பொறியியல் திணைக்களம்'], 'url' => 'https://engdept.nw.gov.lk'],
        ['name' => ['si' => 'සෞඛ්‍ය දෙපාර්තමේන්තුව', 'en' => 'Dept. of Health', 'ta' => 'சுகாதாரத் திணைக்களம்'], 'url' => 'https://healthdept.nw.gov.lk'],
        ['name' => ['si' => 'නිවාස හා ඉදිකිරීම් දෙපාර්තමේන්තුව', 'en' => 'Dept. of Housing', 'ta' => 'வீட்டுவசதி திணைக்களம்'], 'url' => 'https://housedep-north-western-province-council-production-3.apps.red-k8s.akaza.lk'],
        ['name' => ['si' => 'කර්මාන්ත සංවර්ධන දෙපාර්තමේන්තුව', 'en' => 'Dept. of Industries', 'ta' => 'கைத்தொழில் அபிவிருத்தி திணைக்களம்'], 'url' => 'https://indep.nw.gov.lk'],
        ['name' => ['si' => 'අභ්‍යන්තර විගණන දෙපාර්තමේන්තුව', 'en' => 'Dept. of Internal Audit', 'ta' => 'உள் தணிக்கை துறை '], 'url' => 'https://interauditdep.nw.gov.lk'],
        ['name' => ['si' => 'වාරිමාර්ග දෙපාර්තමේන්තුව', 'en' => 'Dept. of Irrigation', 'ta' => 'நீர்ப்பாசனத் திணைக்களம்'], 'url' => 'https://irridept.nw.gov.lk'],
        ['name' => ['si' => 'ඉඩම් කොමසාරිස් දෙපාර්තමේන්තුව', 'en' => 'Dept. of Land Commissioner', 'ta' => ' நில ஆணையர் துறை'], 'url' => 'https://landdept.nw.gov.lk'],
        ['name' => ['si' => 'පළාත් පාලන දෙපාර්තමේන්තුව', 'en' => 'Dept. of Local Government', 'ta' => 'உள்ளூராட்சி திணைக்களம்'], 'url' => 'https://dolgnwp.lk'],
        ['name' => ['si' => 'පරිවාස හා ළමාරක්ෂක සේවා දෙපාර්තමේන්තුව', 'en' => 'Dept. of Probation and Childcare', 'ta' => 'நன்னடத்தை மற்றும் குழந்தை பராமரிப்பு துறை'], 'url' => 'https://probcc.nw.gov.lk'],
        ['name' => ['si' => 'පළාත් ආදායම් දෙපාර්තමේන්තුව', 'en' => 'Dept. of Revenue', 'ta' => 'வருமானத் திணைக்களம்'], 'url' => 'https://prorevdept.nw.gov.lk'],
        ['name' => ['si' => 'මාර්ග සංවර්ධන දෙපාර්තමේන්තුව', 'en' => 'Dept. of Road Development', 'ta' => 'வீதி அபிவிருத்தி திணைக்களம்'], 'url' => 'https://prdd.nw.gov.lk'],
        ['name' => ['si' => 'ග්‍රාම සංවර්ධන දෙපාර්තමේන්තුව', 'en' => 'Dept. of Rural Development', 'ta' => 'ஊரக வளர்ச்சித் துறை'], 'url' => 'https://rural-web-north-western-province-council-production-3.apps.red-k8s.akaza.lk'],
        ['name' => ['si' => 'සමාජ සේවා දෙපාර්තමේන්තුව', 'en' => 'Dept. of Social Services', 'ta' => 'சமூக சேவைகள் திணைக்களம்'], 'url' => 'https://socialdept.nw.gov.lk'],
    ],
    'D' => [
        ['name' => ['si' => 'සමූපකාර සේවක කොමිෂන් සභාව', 'en' => 'Co-op Employees Commission', 'ta' => 'கூட்டுறவு ஊழியர் ஆணைக்குழு'], 'url' => 'https://coopempcomm.nw.gov.lk'],
        ['name' => ['si' => 'වයඹ පුහුණු ආයතනය', 'en' => 'Wayamba Training Institute', 'ta' => 'வயம்ப பயிற்சி நிறுவனம்'], 'url' => 'https://wti.nw.gov.lk'],
    ],
    'E' => [
        ['name' => ['si' => 'කර්මාන්ත සේවා කාර්යාංශය (ISB)', 'en' => 'Industrial Services Bureau', 'ta' => 'கைத்தொழில் சேவைகள் பணியகம்'], 'url' => 'https://www.isb.lk'],
        ['name' => ['si' => 'වයඹ සංවර්ධන අධිකාරිය', 'en' => 'Wayamba Development Authority', 'ta' => 'வயம்ப அபிவிருத்தி அதிகாரசபை'], 'url' => 'https://wda.lk'],
        ['name' => ['si' => 'මානව සම්පත් සංවර්ධන අධිකාරිය', 'en' => 'HR Development Authority', 'ta' => 'மனித வள அபிவிருத்தி அதிகாரசபை'], 'url' => 'https://hrda.nw.gov.lk'],
        ['name' => ['si' => 'පරිසර අධිකාරිය (PEA)', 'en' => 'Provincial Environmental Authority', 'ta' => 'மாகாண சுற்றாடல் அதிகாரசபை'], 'url' => 'https://pea.nw.gov.lk'],
        ['name' => ['si' => 'මාර්ගස්ථ මගී ප්‍රවාහන අධිකාරිය', 'en' => 'Road Passenger Transport Authority', 'ta' => 'வீதி பயணிகள் போக்குவரத்து அதிகாரசபை'], 'url' => 'https://rpta.nw.gov.lk'],
		
    ],
    'F' => [
        ['name' => ['si' => 'පුරවැසි ලියාපදිංචි පද්ධතිය (MeetMe)', 'en' => 'Citizen Registration System (MeetMe)', 'ta' => 'பிரஜைகள் பதிவு முறைமை (MeetMe)'], 'url' => 'https://meetme.nw.gov.lk'],
        ['name' => ['si' => 'විනිවිද ප්‍රමාණ පත්‍ර (BOQ)', 'en' => 'BOQ Citizen Portal', 'ta' => 'BOQ பிரஜை போர்டல்'], 'url' => 'https://boq.nw.gov.lk'],
        ['name' => ['si' => 'භූගෝලීය තොරතුරු පද්ධතිය (GIS)', 'en' => 'Geo-Information System', 'ta' => 'புவியியல் தகவல் அமைப்பு'], 'url' => 'https://geoprovision.nw.gov.lk'],
        ['name' => ['si' => 'කළමනාකරණ තොරතුරු පද්ධතිය (MIS)', 'en' => 'Management Info System', 'ta' => 'மேலாண்மை தகவல் அமைப்பு'], 'url' => 'https://mis.nw.gov.lk'],
        ['name' => ['si' => 'පළාත් සභාවේ වාහන වෙන්කරගැනීමේ පද්ධතිය (VFBMS)', 'en' => 'Vehicle Fleet & Booking Management', 'ta' => 'மாகாண சபை வாகன முன்பதிவு முறை'], 'url' => 'https://vehicle-nwp-north-western-province-council-production-3.apps.red-k8s.akaza.lk'],
    ]
];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); }
        .whatsapp-pulse { animation: pulse-green 2s infinite; }
        @keyframes pulse-green { 0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); } 70% { box-shadow: 0 0 0 15px rgba(34, 197, 94, 0); } 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } }
        .chat-window { transition: all 0.3s ease; transform: translateY(120%); }
        .chat-window.active { transform: translateY(0); }
        .category-container { position: relative; padding-bottom: 2rem; margin-bottom: 3rem; border-bottom: 2px dashed #e2e8f0; }
        .category-header { display: inline-flex; align-items: center; padding: 0.75rem 2rem; border-radius: 0 50px 50px 0; margin-left: -1.5rem; color: white; font-weight: bold; box-shadow: 4px 4px 15px rgba(0,0,0,0.1); }
        .bg-cat-A { background: linear-gradient(90deg, #1e3a8a, #3b82f6); }
        .bg-cat-B { background: linear-gradient(90deg, #047857, #10b981); }
        .bg-cat-C { background: linear-gradient(90deg, #b91c1c, #ef4444); }
        .bg-cat-D { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
        .bg-cat-E { background: linear-gradient(90deg, #ea580c, #fb923c); }
        .bg-cat-F { background: linear-gradient(90deg, #334155, #64748b); }
        @keyframes scale-up { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .animate-scale-up { animation: scale-up 0.3s ease-out; }

        /* Suggestion buttons style */
        .suggestion-btn {
            @apply bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs px-3 py-1.5 rounded-xl border border-blue-200 transition-all;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

     <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-40 border-b">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5f/Emblem_of_Sri_Lanka.svg" class="h-10" alt="Logo">
                <h1 class="text-blue-900 font-bold text-lg hidden md:block uppercase"><?php echo $t['title']; ?></h1>
            </div>
            <div class="flex space-x-1 bg-gray-100 p-1 rounded-xl">
                <a href="?lang=si" class="px-4 py-2 rounded-lg text-xs font-bold <?php echo $lang=='si'?'bg-blue-600 text-white':'text-gray-500 hover:bg-gray-200'; ?>">සිංහල</a>
                <a href="?lang=en" class="px-4 py-2 rounded-lg text-xs font-bold <?php echo $lang=='en'?'bg-blue-600 text-white':'text-gray-500 hover:bg-gray-200'; ?>">English</a>
                <a href="?lang=ta" class="px-4 py-2 rounded-lg text-xs font-bold <?php echo $lang=='ta'?'bg-blue-600 text-white':'text-gray-500 hover:bg-gray-200'; ?>">தமிழ்</a>
            </div>
        </div>
    </nav>

    <header class="gradient-bg text-white py-20 px-6 text-center">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl md:text-5xl font-extrabold mb-4"><?php echo $t['welcome']; ?></h2>
            <p class="text-lg text-blue-200 mb-10"><?php echo $t['subtitle']; ?></p>
            <div class="relative max-w-2xl mx-auto">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="searchInput" onkeyup="searchLinks()" placeholder="<?php echo $t['search_placeholder']; ?>" 
                       class="w-full pl-14 pr-6 py-4 rounded-2xl text-slate-900 shadow-2xl focus:ring-4 focus:ring-blue-500/20 outline-none transition-all">
            </div>
        </div>
    </header>

    <div id="messageModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-scale-up">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center space-x-4">
                        <div id="modalImgBox" class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center">
                            <i id="placeholderIcon" class="fas fa-user text-3xl text-gray-300 hidden"></i>
                            <img id="modalImg" src="" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 id="modalName" class="font-bold text-blue-900 text-lg"></h4>
                            <p id="modalTitle" class="text-sm text-gray-500 uppercase"></p>
                        </div>
                    </div>
                    <button onclick="closeMessage()" class="text-gray-400 hover:text-red-500 text-2xl">&times;</button>
                </div>
                <div id="modalText" class="text-gray-700 leading-relaxed italic border-l-4 border-blue-600 pl-6 py-2 text-lg"></div>
            </div>
        </div>
    </div>

    <!-- Leadership Section -->
    <section class="container mx-auto py-16 px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-blue-900 uppercase tracking-wide">පළාත් නායකත්වය | Leadership</h2>
            <div class="w-24 h-1 bg-yellow-400 mx-auto mt-2"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="group bg-white rounded-3xl p-8 pt-20 text-center shadow-xl border border-gray-100 hover:shadow-2xl transition-all relative">
                <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-32 h-32 rounded-2xl border-4 border-white overflow-hidden shadow-lg bg-blue-50">
                    <img src="pic/govnew.png" alt="Governor" class="w-full h-full object-cover">
                </div>
                <h3 class="text-xl font-bold text-blue-900">Governor - NWP</h3>
                <p class="text-sm text-gray-500 mb-6 uppercase">ගරු වයඹ පළාත් ආණ්ඩුකාරවර</p>
                <button onclick="openMessage('gov')" class="bg-blue-900 text-white px-6 py-2 rounded-full text-sm font-bold hover:bg-blue-700 transition">Read Message</button>
            </div>
            <div class="group bg-white rounded-3xl p-8 pt-20 text-center shadow-xl border border-gray-100 hover:shadow-2xl transition-all relative">
                <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-32 h-32 rounded-2xl border-4 border-white overflow-hidden shadow-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-user-tie text-5xl text-blue-200"></i>
                </div>
                <h3 class="text-xl font-bold text-blue-900">Chief Minister</h3>
                <p class="text-sm text-gray-500 mb-6 uppercase">ගරු පළාත් ප්‍රධාන අමාත්‍ය </p>
                <button onclick="openMessage('cm')" class="bg-blue-600 text-white px-6 py-2 rounded-full text-sm font-bold hover:bg-blue-500 transition">Read Message</button>
            </div>
            <div class="group bg-white rounded-3xl p-8 pt-20 text-center shadow-xl border border-gray-100 hover:shadow-2xl transition-all relative">
                <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-32 h-32 rounded-2xl border-4 border-white overflow-hidden shadow-lg bg-blue-50">
                    <img src="pic/csnew.png" alt="Chief Secretary" class="w-full h-full object-cover">
                </div>
                <h3 class="text-xl font-bold text-blue-900">Chief Secretary</h3>
                <p class="text-sm text-gray-500 mb-6 uppercase">වයඹ ප්‍රධාන ලේකම්</p>
                <button onclick="openMessage('cs')" class="bg-blue-900 text-white px-6 py-2 rounded-full text-sm font-bold hover:bg-blue-700 transition">Read Message</button>
            </div>
        </div>
    </section>

    <!-- Main Links -->
    <main class="container mx-auto py-12 px-6">
        <?php 
        $cats = [
            'A' => ['t' => $t['cat_a'], 'i' => 'fa-building-columns'],
            'B' => ['t' => $t['cat_b'], 'i' => 'fa-landmark'],
            'C' => ['t' => $t['cat_c'], 'i' => 'fa-city'],
            'D' => ['t' => $t['cat_d'], 'i' => 'fa-users-gear'],
            'E' => ['t' => $t['cat_e'], 'i' => 'fa-sitemap'],
            'F' => ['t' => $t['cat_f'], 'i' => 'fa-laptop-code']
        ];
        foreach($cats as $key => $info): 
        ?>
        <section class="category-container" id="sec-<?php echo $key; ?>">
            <div class="category-header bg-cat-<?php echo $key; ?> mb-8">
                <i class="fas <?php echo $info['i']; ?> mr-3"></i> <?php echo $info['t']; ?>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($links[$key] as $link): ?>
                <div class="link-card bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <h4 class="font-bold text-slate-800 mb-4 h-12 overflow-hidden"><?php echo $link['name'][$lang]; ?></h4>
                    <a href="<?php echo $link['url']; ?>" target="_blank" class="text-blue-600 font-bold text-sm flex items-center group-hover:text-blue-700">
                        <?php echo $t['visit']; ?> <i class="fas fa-arrow-right ml-2 text-xs transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endforeach; ?>
    </main>

    <!-- Footer  -->
    <footer class="bg-slate-900 text-slate-400 py-16 px-6">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 text-left mb-12">
                
                <div class="space-y-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5f/Emblem_of_Sri_Lanka.svg" class="h-16 mb-4 opacity-80" alt="Emblem">
                    <p class="text-white font-bold uppercase text-sm"><?php echo $t['title']; ?></p>
                    <p class="text-xs leading-relaxed"><?php echo $t['subtitle']; ?></p>
                    <div class="flex space-x-4 pt-4">
                        <a href="#" class="hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-white transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-white transition"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6 text-sm uppercase tracking-wider"><?php echo $t['gov_links']; ?></h4>
                    <ul class="space-y-3 text-xs">
                        <li><a href="https://www.presidentsoffice.gov.lk" target="_blank" class="hover:text-blue-400 transition flex items-center"><i class="fas fa-chevron-right mr-2 text-[10px]"></i> <?php echo $t['presidency']; ?></a></li>
                        <li><a href="https://www.pmoffice.gov.lk" target="_blank" class="hover:text-blue-400 transition flex items-center"><i class="fas fa-chevron-right mr-2 text-[10px]"></i> <?php echo $t['pm_office']; ?></a></li>
                        <li><a href="http://www.pubad.gov.lk" target="_blank" class="hover:text-blue-400 transition flex items-center"><i class="fas fa-chevron-right mr-2 text-[10px]"></i> <?php echo $t['pub_ad']; ?></a></li>
                        <li><a href="https://www.treasury.gov.lk" target="_blank" class="hover:text-blue-400 transition flex items-center"><i class="fas fa-chevron-right mr-2 text-[10px]"></i> <?php echo $t['finance']; ?></a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6 text-sm uppercase tracking-wider"><?php echo $t['important_links']; ?></h4>
                    <ul class="space-y-3 text-xs">
                        <li><a href="http://www.mpcllgp.gov.lk" target="_blank" class="hover:text-blue-400 transition flex items-center"><i class="fas fa-chevron-right mr-2 text-[10px]"></i> <?php echo $t['pc_min']; ?></a></li>
                        <li><a href="http://www.gic.gov.lk" target="_blank" class="hover:text-yellow-400 transition flex items-center text-yellow-500 font-semibold"><i class="fas fa-phone-alt mr-2 text-[10px]"></i> <?php echo $t['gic']; ?></a></li>
                        <li><a href="https://www.gov.lk" target="_blank" class="hover:text-blue-400 transition flex items-center"><i class="fas fa-globe mr-2 text-[10px]"></i> Sri Lanka Government Portal</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6 text-sm uppercase tracking-wider">Contact |  සම්බන්ධීකරණය</h4>
                    <ul class="space-y-3 text-xs">
                        <li class="flex items-start italic"><i class="fas fa-map-marker-alt mr-3 mt-1"></i> <?php echo $t['address']; ?></li>
                        <li><i class="fas fa-phone mr-3"></i> +94 37 2237169</li>
                        <li><i class="fas fa-envelope mr-3"></i> csnwp@sltnet.lk</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center text-[10px] opacity-60">
                <p><?php echo $t['footer_text']; ?></p>
                <p class="mt-4 md:mt-0">&copy; <?php echo date('Y'); ?>Digital Division | North Western Provincial Council. All Rights Reserved.</p>
            </div>
        </div>
    </footer>


<!-- Chatbot -->
<div class="fixed bottom-6 right-6 flex flex-col items-end space-y-4 z-50">
    <a href="https://wa.me/94705995577" target="_blank" class="whatsapp-pulse bg-green-500 text-white px-6 py-3 rounded-full shadow-2xl flex items-center space-x-3 hover:bg-green-600 transition-all group">
        <span class="font-bold text-sm hidden group-hover:block transition-all"><?php echo $t['ask_secretary']; ?></span>
        <i class="fab fa-whatsapp text-2xl"></i>
    </a>
    <button onclick="toggleChat()" class="bg-blue-600 text-white p-4 rounded-full shadow-2xl hover:scale-110 transition-all">
        <i class="fas fa-robot text-2xl"></i>
    </button>
</div>

<!-- Professional Chat Window with Fixed Suggestions -->
<div id="chatBox" class="chat-window fixed bottom-6 right-6 w-full max-w-[380px] md:max-w-[420px] lg:max-w-[460px] bg-white rounded-3xl shadow-2xl z-50 overflow-hidden border transition-all duration-300">
    
    <!-- Header -->
    <div class="bg-blue-900 p-4 text-white flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-robot text-xl"></i>
            </div>
            <div>
                <span class="font-bold text-sm"><?php echo $t['bot_title']; ?></span>
            </div>
        </div>
        <button onclick="toggleChat()" class="text-white hover:text-red-300 text-2xl leading-none">&times;</button>
    </div>

    <!-- Chat Body -->
    <div id="chatBody" class="h-[420px] md:h-[460px] lg:h-[480px] overflow-y-auto p-4 space-y-4 text-sm bg-slate-50">
        <div class="bg-white p-4 rounded-2xl rounded-tl-none border shadow-sm">
            <?php echo $t['bot_msg']; ?>
        </div>
    </div>

    <!-- Fixed Suggested Questions -->
    <div class="px-4 pt-2 pb-3 bg-white border-t">
        <p class="text-xs text-gray-500 mb-2 pl-1">Common Questions</p>
        <div id="fixedSuggestions" class="flex flex-wrap gap-2"></div>
    </div>

    <!-- Input Area -->
    <div class="p-4 border-t bg-white">
        <div class="flex space-x-2">
            <input 
                type="text" 
                id="chatInput" 
                class="flex-1 bg-slate-100 rounded-2xl px-5 py-3.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-400" 
                placeholder="Type your question here..." 
            >
            <button onclick="sendMsg()" class="bg-blue-600 hover:bg-blue-700 text-white w-11 h-11 rounded-2xl flex items-center justify-center transition-all flex-shrink-0">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

    <script>
    const currentLang = "<?php echo $lang; ?>";
    const knowledgeBase = <?php echo $jsonKnowledge; ?>;

    function normalize(text) {
        return text.toLowerCase().replace(/[^\p{L}\p{N}\s]/gu, "").trim();
    }

    function wordSimilarity(w1, w2) {
        if (!w1 || !w2) return 0;
        const len = Math.max(w1.length, w2.length);
        let dist = 0;
        const dp = Array.from({length: w1.length + 1}, () => Array(w2.length + 1).fill(0));
        for (let i = 0; i <= w1.length; i++) dp[i][0] = i;
        for (let j = 0; j <= w2.length; j++) dp[0][j] = j;
        for (let i = 1; i <= w1.length; i++) {
            for (let j = 1; j <= w2.length; j++) {
                const cost = w1[i-1] === w2[j-1] ? 0 : 1;
                dp[i][j] = Math.min(dp[i-1][j] + 1, dp[i][j-1] + 1, dp[i-1][j-1] + cost);
            }
        }
        dist = dp[w1.length][w2.length];
        return Math.max(0, 1 - dist / len);
    }

    function detectIntent(input) {
        const clean = normalize(input);
        if (clean.length < 3) return null;

        let bestMatch = null;
        let bestScore = 0;
        const userWords = clean.split(/\s+/).filter(w => w.length > 1);

        knowledgeBase.forEach(item => {
            let score = 0;
            item.keywords.forEach(kw => {
                const keyNorm = normalize(kw);
                const keyWords = keyNorm.split(/\s+/);
                let maxSim = 0;
                userWords.forEach(u => {
                    keyWords.forEach(k => {
                        const sim = wordSimilarity(u, k);
                        if (sim > maxSim) maxSim = sim;
                    });
                });
                score += maxSim * (maxSim > 0.75 ? 9 : (maxSim > 0.55 ? 5 : 2));
            });

            if (score > bestScore) {
                bestScore = score;
                bestMatch = item;
            }
        });

        return (bestScore >= 5.5) ? bestMatch : null;
    }

    function getResponse(input) {
        const intent = detectIntent(input);
        if (intent) {
            return intent.responses[currentLang] || intent.responses["en"] || intent.responses["si"];
        }
        return getFallback();
    }

    function getFallback() {
        const fallbacks = {
            si: "කණගාටුයි, මට ඒ ගැන තොරතුරු නැහැ. කරුණාකර වෙනත් විදියකින් අහන්න හෝ ප්‍රධාන ලේකම් කාර්යාලයට සම්බන්ධ වන්න (037-2237169).",
            en: "Sorry, I don't have information about that. Please try rephrasing your question or contact Chief Secretary's Office at 037-2237169.",
            ta: "மன்னிக்கவும், அந்த தகவல் என்னிடம் இல்லை. வேறு விதமாக கேளுங்கள் அல்லது பிரதான செயலாளர் அலுவலகத்தை தொடர்பு கொள்ளவும் (037-2237169)."
        };
        return fallbacks[currentLang] || fallbacks.en;
    }

// Fixed Professional Suggestions
function loadFixedSuggestions() {
    const container = document.getElementById('fixedSuggestions');
    container.innerHTML = '';

    const fixedQuestions = [
        "හොස්පිටල් ගැන",
        "ප්‍රධාන ලේකම් කාර්යාලය",
        "උපත් සහතිය ගන්නේ කොහොමද",
        "සෞඛ්‍ය අමාත්‍යංශය",
        "වාහන බුකින් tdfg"
    ];

    fixedQuestions.forEach(question => {
        const btn = document.createElement('button');
        btn.className = 'suggestion-btn bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs px-4 py-2 rounded-2xl border border-blue-200 transition-all whitespace-nowrap';
        btn.textContent = question;
        btn.onclick = () => {
            document.getElementById('chatInput').value = question;
            sendMsg();
        };
        container.appendChild(btn);
    });
}

    // Real-time suggestions while typing
    function showSuggestions(text) {
        const box = document.getElementById('suggestionsBox');
        box.innerHTML = '';

        if (text.length < 2) return;

        const suggestions = [
            "හොස්පිටල් ගැන",
            "ප්‍රධාන ලේකම් කාර්යාලය",
            "උපත් සහතිය ගන්නේ කොහොමද",
            "සෞඛ්‍ය අමාත්‍යංශය",
            "වාහන බුකින්",
            "ඉඩම් ගැන තොරතුරු",
            "උපත් සහතිය",
            "ලේකම් කාර්යාලය",
            "hospital"
        ];

        suggestions.forEach(s => {
            if (s.toLowerCase().includes(text.toLowerCase())) {
                const btn = document.createElement('button');
                btn.className = 'suggestion-btn bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs px-3 py-1.5 rounded-xl border border-blue-200 transition-all';
                btn.textContent = s;
                btn.onclick = () => {
                    document.getElementById('chatInput').value = s;
                    sendMsg();
                };
                box.appendChild(btn);
            }
        });
    }


    async function logUnknownQuestion(question) {
        if (question.length < 4) return;
        try {
            await fetch('log_unknown.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ question: question, lang: currentLang, timestamp: new Date().toISOString() })
            });
        } catch (e) {}
    }

    function sendMsg() {
        const inputEl = document.getElementById('chatInput');
        const body = document.getElementById('chatBody');
        const userText = inputEl.value.trim();
        if (!userText) return;

        body.innerHTML += `<div class='flex justify-end'><div class='bg-blue-600 text-white p-3 rounded-2xl rounded-tr-none max-w-[80%]'>${userText}</div></div>`;

        const reply = getResponse(userText);

        setTimeout(() => {
            body.innerHTML += `<div class='bg-white p-3 rounded-2xl rounded-tl-none border max-w-[80%]'>${reply}</div>`;
            body.scrollTop = body.scrollHeight;

            if (!detectIntent(userText)) {
                logUnknownQuestion(userText);
            }
        }, 400);

        inputEl.value = '';
        document.getElementById('suggestionsBox').innerHTML = '';
    }

    // Initialize when chat opens
    function toggleChat() {
    const chatBox = document.getElementById('chatBox');
    chatBox.classList.toggle('active');
    
    if (chatBox.classList.contains('active')) {
        setTimeout(loadFixedSuggestions, 300);
    }
}

    const messages = {
        gov: { name: "Hon. Thissa Kumarasiri Warnasuriya", title: "Governor - NWP", text: "වයඹ පළාතේ සංවර්ධනය උදෙසා ඩිජිටල් තාක්ෂණය උපරිමයෙන් භාවිත කරමින් කාර්යක්ෂම රාජ්‍ය සේවාවක් සැපයීම අපගේ අරමුණයි.", img: "https://governor.nw.gov.lk/images/governor_img.jpg" },
        cm: { name: "Chief Minister", title: "Executive Leadership", text: "No Message.", img: "" },
        cs: { name: "Mr. I.M. Indika Ilangakoon", title: "Chief Secretary - NWP", text: "තොරතුරු තාක්ෂණය ඔස්සේ රාජ්‍ය පාලනය නවීකරණය කිරීම තුළින් පරිපාලන ක්‍රියාවලිය වඩාත් සරල හා වේගවත් කිරීමට අප බලාපොරොත්තු වෙමු.", img: "https://cs.nw.gov.lk/images/cs_img.jpg" }
    };

    function openMessage(type) {
        const data = messages[type];
        document.getElementById('modalName').innerText = data.name;
        document.getElementById('modalTitle').innerText = data.title;
        document.getElementById('modalText').innerText = `"${data.text}"`;
        const imgEl = document.getElementById('modalImg');
        const iconEl = document.getElementById('placeholderIcon');
        if(data.img) { 
            imgEl.src = data.img; 
            imgEl.classList.remove('hidden'); 
            iconEl.classList.add('hidden'); 
        } else { 
            imgEl.classList.add('hidden'); 
            iconEl.classList.remove('hidden'); 
        }
        document.getElementById('messageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeMessage() {
        document.getElementById('messageModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function searchLinks() {
        let q = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('.link-card').forEach(c => {
            c.style.display = c.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
        document.querySelectorAll('.category-container').forEach(sec => {
            const visible = sec.querySelectorAll('.link-card:not([style*="display: none"])').length;
            sec.style.display = (visible === 0 && q !== "") ? 'none' : '';
        });
    }

    // Initialize event listeners
    document.addEventListener("DOMContentLoaded", () => {
        const chatInput = document.getElementById("chatInput");
        
        chatInput.addEventListener("keypress", function(e) {
            if (e.key === "Enter") sendMsg();
        });

        chatInput.addEventListener("keyup", function() {
            showSuggestions(this.value);
        });
    });
</script>
</body>
</html>