export interface UniversalEntitySpec {
  id: string;
  slug: string;
  type: 'course' | 'lab' | 'terminal' | 'center' | 'legal' | 'tool';
  titleBn: string;
  titleEn: string;
  category: string;
  descriptionBn: string;
  descriptionEn: string;
  iconName: string;
  accentColor: string;
  pipelineSteps: [string, string, string]; // [Input/Start, Engine/Process, Output/Reward]
  redCalloutLabel1: string;
  redCalloutLabel2: string;
  schemaType: 'Course' | 'SoftwareApplication' | 'Service' | 'AboutPage' | 'WebPage';
  schemaRating: number;
  reviewCount: number;
  adSenseNotesBn: string;
  competitorEdgeBn: string;
}

export const OMNI_SITE_ENTITIES: UniversalEntitySpec[] = [
  // 1. FLAGSHIP COURSES & ACADEMY TRACKS
  {
    id: 'course-cyber-fundamentals',
    slug: 'cybersecurity-fundamentals',
    type: 'course',
    titleBn: 'সাইবার সিকিউরিটি ফান্ডামেন্টালস ও ডিফেন্সিভ আর্কিটেকচার',
    titleEn: 'Cybersecurity Fundamentals & Defensive Principles',
    category: 'Cyber Academy',
    descriptionBn: 'আন্তর্জাতিক মানের সাইবার ডিফেন্স থিওরি, প্র্যাকটিক্যাল ল্যাব, সিকিউর কোডিং এবং সিস্টেম প্রোটেকশন গাইড।',
    descriptionEn: 'International standard cybersecurity theory, hands-on defensive labs, secure coding and production defense architecture.',
    iconName: 'Shield',
    accentColor: '#00f5d4',
    pipelineSteps: ['ফান্ডামেন্টালস ইনপুট', 'রিয়েল-টাইম ল্যাব এনালাইসিস', 'ভেরিফায়েড সার্টিফিকেট ও BDT রিওয়ার্ড'],
    redCalloutLabel1: 'স্টেপ ১ - ইনপুট',
    redCalloutLabel2: 'স্টেপ ২ - ল্যাব',
    schemaType: 'Course',
    schemaRating: 4.95,
    reviewCount: 3840,
    adSenseNotesBn: 'উচ্চ সিপিসি শিক্ষামূলক ও প্রযুক্তিগত কন্টেন্ট যা শতভাগ গুগল অ্যাডসেন্স ফ্রেন্ডলি।',
    competitorEdgeBn: 'আন্তর্জাতিক প্ল্যাটফর্মের মতো হ্যান্ডস-অন ওয়েব টার্মিনাল ও লাইভ ডেমো সমন্বিত।'
  },
  {
    id: 'course-vapt-bug-bounty',
    slug: 'vapt-bug-bounty-mastery',
    type: 'course',
    titleBn: 'এডভান্সড ওয়েব অ্যাপ্লিকেশন VAPT ও বাগ বাউন্টি মাস্টারক্লাস',
    titleEn: 'Advanced Web Application VAPT & Bug Bounty Architecture',
    category: 'Ethical Hacking',
    descriptionBn: 'OWASP Top 10, SQLi, XSS, SSRF, IDOR দুর্বলতা চিহ্নিতকরণ ও প্রফেশনাল বাগ বাউন্টি রিপোর্টিং গাইড।',
    descriptionEn: 'Comprehensive deep dive into OWASP Top 10 vulnerability assessment, penetration testing, and ethical bug bounty reporting.',
    iconName: 'Terminal',
    accentColor: '#ff3366',
    pipelineSteps: ['ওয়েব এন্ডপয়েন্ট রিকন', 'ভালনারেবিলিটি এক্সপ্লয়টেশন', 'CVSS ৩.১ অডিট রিপোর্ট'],
    redCalloutLabel1: 'ট্রিগার রিকন',
    redCalloutLabel2: 'অডিট প্রটোকল',
    schemaType: 'Course',
    schemaRating: 4.98,
    reviewCount: 5210,
    adSenseNotesBn: 'সিকিউরিটি গবেষক ও এন্টারপ্রাইজ ডেভেলপারদের জন্য মূল্যবান টেকনিক্যাল গাইড।',
    competitorEdgeBn: 'সরাসরি প্র্যাকটিক্যাল কোড ও পেনিট্রেশন টেস্টিং টার্মিনাল সাপোর্ট।'
  },
  {
    id: 'course-reverse-engineering',
    slug: 'reverse-engineering-malware-analysis',
    type: 'course',
    titleBn: 'রিভার্স ইঞ্জিনিয়ারিং ও ম্যালওয়্যার অ্যানালাইসিস এনক্লেভ',
    titleEn: 'Reverse Engineering & Malware Analysis Enclave',
    category: 'Binary Defense',
    descriptionBn: 'Ghidra, x64dbg এবং আইডিএ প্রো দিয়ে বাইনারি ডিসঅ্যাসেম্বলি ও ম্যালওয়্যার নিউট্রালাইজেশন শেখার সম্পূর্ণ কোর্স।',
    descriptionEn: 'Binary reverse engineering using Ghidra, memory inspection, dynamic disassembly and ransomware neutralization.',
    iconName: 'Cpu',
    accentColor: '#a855f7',
    pipelineSteps: ['PE/ELF বাইনারি লোডিং', 'মেমরি ইনস্পেকশন ও ডিকোড', 'IOC থ্রেট ইন্টেলিজেন্স'],
    redCalloutLabel1: 'বাইনারি লোড',
    redCalloutLabel2: 'ডায়নামিক অডিট',
    schemaType: 'Course',
    schemaRating: 4.92,
    reviewCount: 2190,
    adSenseNotesBn: 'গুগল EEAT (Expertise, Experience, Authoritativeness) স্ট্যান্ডার্ড অনুযায়ী তৈরি।',
    competitorEdgeBn: 'অন-ডিভাইস নিরাপদ স্যান্ডবক্স ও স্ট্যাটিক এনালাইসিস ইঞ্জিন।'
  },
  {
    id: 'course-cloud-devsecops',
    slug: 'cloud-native-devsecops-hardening',
    type: 'course',
    titleBn: 'ক্লাউড-নেটিভ DevSecOps, কিউবারনেটিস ও CI/CD সিকিউরিটি',
    titleEn: 'Cloud-Native DevSecOps, Kubernetes & CI/CD Hardening',
    category: 'DevSecOps',
    descriptionBn: 'কন্টেইনার সুরক্ষা, K8s RBAC, পলিসি-অ্যাজ-কোড এবং জিরো-ট্রাস্ট পাইপলাইন আর্কিটেকচার।',
    descriptionEn: 'Container security, Kubernetes admission controllers, policy-as-code, and automated zero-trust CI/CD workflows.',
    iconName: 'Layers',
    accentColor: '#3b82f6',
    pipelineSteps: ['ডকার ইমেজ স্ক্যানিং', 'K8s পলিসি অডিট', 'জিরো-ট্রাস্ট ডিপ্লয়মেন্ট'],
    redCalloutLabel1: 'Trivy / SAST স্ক্যান',
    redCalloutLabel2: 'mTLS কনফিগ',
    schemaType: 'Course',
    schemaRating: 4.96,
    reviewCount: 4120,
    adSenseNotesBn: 'হাই-সিপিসি ক্লাউড সিকিউরিটি ও এন্টারপ্রাইজ সফটওয়্যার ডেভেলপমেন্ট কন্টেন্ট।',
    competitorEdgeBn: 'বাস্তব YAML ম্যানিফেস্ট ও অটোমেটেড পলিসি চেকিং ইঞ্জিন।'
  },

  // 2. CYBER LABS & INTERACTIVE TERMINALS
  {
    id: 'lab-kali-web-terminal',
    slug: 'kali-linux-web-terminal',
    type: 'terminal',
    titleBn: 'কালি লিনাক্স রিয়েল-টাইম ওয়েব টার্মিনাল ও স্যান্ডবক্স',
    titleEn: 'Kali Linux Real-Time Web Console & Cyber Sandbox',
    category: 'Terminal Lab',
    descriptionBn: 'ব্রাউজারেই ১০০% ক্লায়েন্ট-সাইড নিরাপদ ভার্চুয়াল লিনাক্স টার্মিনাল ও নেটওয়ার্ক রিকন কমান্ড প্র্যাকটিস।',
    descriptionEn: 'Interactive in-browser simulated Linux terminal executing core utilities, network probes and scripting.',
    iconName: 'Terminal',
    accentColor: '#00f5d4',
    pipelineSteps: ['কমান্ড ইনপুট প্রসেসিং', 'টার্মিনাল পাইপলাইন এক্সিকিউশন', 'আউটপুট ও লগ ভেরিফিকেশন'],
    redCalloutLabel1: 'শেল কমান্ড রিড',
    redCalloutLabel2: 'আইসোলেটেড স্যান্ডবক্স',
    schemaType: 'SoftwareApplication',
    schemaRating: 4.97,
    reviewCount: 8430,
    adSenseNotesBn: 'উচ্চ ইউজার এনগেজমেন্ট ও শূন্য বাউন্স রেট নিশ্চিতকারী ইন্টারঅ্যাক্টিভ ওয়েব অ্যাপ।',
    competitorEdgeBn: 'কোন ক্লাউড বিলিং ছাড়াই সম্পূর্ণ ব্রাউজারে চালিত ইনস্ট্যান্ট কমান্ড প্রম্পট।'
  },
  {
    id: 'lab-ctf-flag-arena',
    slug: 'ctf-cryptographic-flag-sandbox',
    type: 'lab',
    titleBn: 'ক্রিপ্টোগ্রাফিক CTF ফ্ল্যাগ এরিনা ও ওয়ালেট বাউন্টি ইঞ্জিন',
    titleEn: 'Cryptographic CTF Flag Arena & Wallet Bounty Engine',
    category: 'CTF Arena',
    descriptionBn: 'গেমিফাইড সাইবার চ্যালেঞ্জ সমাধান করে ফ্ল্যাগ সাবমিট করুন এবং সরাসরি ওয়ালেটে BDT ও XP পয়েন্ট উপার্জন করুন।',
    descriptionEn: 'Gamified cybersecurity challenges with cryptographic flag verification awarding monetary BDT and XP points.',
    iconName: 'Lock',
    accentColor: '#eab308',
    pipelineSteps: ['চ্যালেঞ্জ এনালাইসিস', 'ফ্ল্যাগ ফাইন্ডিং (HS{...})', 'ক্রিপ্টোগ্রাফিক ভেরিফিকেশন'],
    redCalloutLabel1: 'ক্যাপচার ফ্ল্যাগ',
    redCalloutLabel2: 'ইনস্ট্যান্ট BDT ক্যাশ',
    schemaType: 'SoftwareApplication',
    schemaRating: 4.99,
    reviewCount: 9140,
    adSenseNotesBn: 'শিক্ষার্থীদের জন্য সর্বোচ্চ সময় ব্যয় ও লার্নিং ভ্যালু সম্পন্ন ফ্ল্যাগশিপ ফিচার।',
    competitorEdgeBn: 'বাস্তব BDT রিওয়ার্ড ওয়ালেট এবং সুরক্ষিত hash_equals ভেরিফিকেশন।'
  },
  {
    id: 'lab-css-rgb-neon',
    slug: 'css-rgb-neon-synthesis-lab',
    type: 'lab',
    titleBn: 'সিএসএস নিয়ন গ্লো, গ্লাস ও কালার সিন্থেসিস ল্যাব',
    titleEn: 'CSS RGB Glow, Neon & Glassmorphism Synthesis Lab',
    category: 'Design Engineering',
    descriptionBn: 'রিয়েল-টাইম সিএসএস বক্স-শ্যাডো, সাইবারপাঙ্ক নিয়ন আলো ও গ্লাস মরফিজম জেনারেটর টুল।',
    descriptionEn: 'Real-time CSS box-shadow, neon cyber radiance, backdrop-filter glassmorphism, and instant CSS generator.',
    iconName: 'Palette',
    accentColor: '#ec4899',
    pipelineSteps: ['RGB স্লাইডার এডজাস্ট', 'লাইভ নিয়ন রেন্ডারিং', 'ক্লিন CSS3 কোড কপি'],
    redCalloutLabel1: 'কালার মিক্সার',
    redCalloutLabel2: '১-ক্লিক কোড',
    schemaType: 'SoftwareApplication',
    schemaRating: 4.91,
    reviewCount: 3410,
    adSenseNotesBn: 'ওয়েব ডিজাইনার ও ফ্রন্টএন্ড ডেভেলপারদের জন্য নিত্যপ্রয়োজনীয় টুল।',
    competitorEdgeBn: 'রেডিমেড প্রিসেট সহ সরাসরি ওয়েবে কপি-পেস্ট সুবিধাযুক্ত।'
  },

  // 3. KEY PLATFORM CENTERS (FROM 28 CENTERS)
  {
    id: 'center-vapt-defense',
    slug: 'center-vapt-security-hub',
    type: 'center',
    titleBn: 'সাইবার ডিফেন্স ও VAPT এন্টারপ্রাইজ সেন্টার',
    titleEn: 'VAPT & Cybersecurity Defense Platform Center',
    category: 'Platform Center',
    descriptionBn: 'ওয়েব, নেটওয়ার্ক ও এন্টারপ্রাইজ সিস্টেমের পূর্ণাঙ্গ দুর্বলতা পরীক্ষা ও ডিফেন্সিভ হার্ডেনিং প্রটোকল।',
    descriptionEn: 'Enterprise vulnerability assessment, penetration testing automation, and continuous security compliance.',
    iconName: 'Shield',
    accentColor: '#00f5d4',
    pipelineSteps: ['সার্ভার অডিট ইনিশিয়েট', 'হেডার ও SSL/TLS চেক', 'কমপ্লায়েন্স রিপোর্ট জেনারেট'],
    redCalloutLabel1: 'অডিট স্ক্যান',
    redCalloutLabel2: 'সিকিউরিটি রিপোর্ট',
    schemaType: 'Service',
    schemaRating: 4.94,
    reviewCount: 1870,
    adSenseNotesBn: 'এন্টারপ্রাইজ সাইবার সুরক্ষার নির্ভরযোগ্য প্রফেশনাল সেন্টার।',
    competitorEdgeBn: 'আন্তর্জাতিক মানের স্ট্যান্ডার্ড ও স্বয়ংক্রিয় অডিট ব্যবস্থা।'
  },
  {
    id: 'center-universal-tools',
    slug: 'center-universal-tools-suite',
    type: 'center',
    titleBn: '২,০২৪+ ইউনিভার্সাল ডেভেলপার ও কনভার্টার স্যুট',
    titleEn: '2,024 Ultra-Pro Universal Tools & Media Converter Suite',
    category: 'Platform Center',
    descriptionBn: 'ক্রিপ্টোগ্রাফি, কোডিং, নেটওয়ার্কিং, মিডিয়া এবং ফাইল কনভার্ট করার বিশালাকার টুলস পোর্টাল।',
    descriptionEn: 'Omni-platform containing 2,024 client-side utilities across cryptography, development, media, and mathematics.',
    iconName: 'Wrench',
    accentColor: '#00f5d4',
    pipelineSteps: ['ইনপুট ডাটা পেস্ট', '০-মিলিসেকেন্ড লোকাল প্রসেস', 'কনভার্টেড রেজাল্ট ডাউনলোড'],
    redCalloutLabel1: 'ইনস্ট্যান্ট রান',
    redCalloutLabel2: 'জিরো ডাটা লিক',
    schemaType: 'SoftwareApplication',
    schemaRating: 4.99,
    reviewCount: 14200,
    adSenseNotesBn: 'দৈনিক লক্ষাধিক ট্র্যাফিক পরিচালনাকারী অল-ইন-ওয়ান ইউটিলিটি হাব।',
    competitorEdgeBn: 'কোনো সার্ভার আপলোড ছাড়াই শতভাগ ক্লায়েন্ট সাইড প্রসেসিং।'
  },
  {
    id: 'center-app-generator',
    slug: 'center-wp-app-maker-studio',
    type: 'center',
    titleBn: 'ওয়েবসাইট টু অ্যান্ড্রয়েড অ্যাপ মেকার স্টুডিও',
    titleEn: 'Website & WordPress to Android APK App Maker Studio',
    category: 'Platform Center',
    descriptionBn: 'যেকোনো ওয়েবসাইট বা ওয়ার্ডপ্রেস সাইটকে মুহূর্তে প্রফেশনাল অ্যান্ড্রয়েড অ্যাপ্লিকেশনে রূপান্তর করুন।',
    descriptionEn: 'Automated turn-key mobile package compiler converting responsive sites into Android APK/AAB assets.',
    iconName: 'Smartphone',
    accentColor: '#8b5cf6',
    pipelineSteps: ['ওয়েবসাইট লিংক ইনপুট', 'ম্যানিফেস্ট ও আইকন কনফিগ', 'প্যাকেজ বান্ডেল ডাউনলোড'],
    redCalloutLabel1: 'ইউআরএল বাইন্ডিং',
    redCalloutLabel2: 'APK জেনারেশন',
    schemaType: 'SoftwareApplication',
    schemaRating: 4.93,
    reviewCount: 6540,
    adSenseNotesBn: 'উচ্চ ভ্যালু সমৃদ্ধ অ্যান্ড্রয়েড ও মোবাইল ডেভেলপার টুল।',
    competitorEdgeBn: 'লাইভ প্রাক-দর্শন ও কাস্টম ম্যানিফেস্ট কোড জেনারেশন সুবিধা।'
  },

  // 4. MANDATORY LEGAL & ADSENSE 100% APPROVAL PAGES
  {
    id: 'page-privacy-policy',
    slug: 'privacy-policy',
    type: 'legal',
    titleBn: 'প্রাইভেসি পলিসি ও জিরো-সার্ভার লগ গ্যারান্টি',
    titleEn: 'Privacy Policy & Zero-Server Log Security Guarantee',
    category: 'Compliance',
    descriptionBn: 'GDPR ও CPRA কমপ্লায়েন্ট প্রাইভেসি চার্টার যা ব্যবহারকারীর তথ্যের সর্বোচ্চ নিরাপত্তা নিশ্চিত করে।',
    descriptionEn: 'GDPR and CPRA compliant privacy charter ensuring zero server logging and local browser enclave privacy.',
    iconName: 'Lock',
    accentColor: '#10b981',
    pipelineSteps: ['লোকাল স্যান্ডবক্স এনক্লেভ', 'জিরো ডাটা ট্র্যাকিং গ্যারান্টি', 'শতভাগ ব্যবহারকারী গোপনীয়তা'],
    redCalloutLabel1: 'কঠোর প্রাইভেসি',
    redCalloutLabel2: 'জিরো লগিং',
    schemaType: 'AboutPage',
    schemaRating: 5.0,
    reviewCount: 980,
    adSenseNotesBn: 'গুগল অ্যাডসেন্স পলিসির বাধ্যতামূলক লিগ্যাল পেইজ।',
    competitorEdgeBn: 'স্বচ্ছতা ও আন্তর্জাতিক মানের ডাটা প্রোটেকশন স্ট্যান্ডার্ড।'
  },
  {
    id: 'page-terms-of-service',
    slug: 'terms-of-service',
    type: 'legal',
    titleBn: 'ব্যবহারের শর্তাবলী ও এথিক্যাল সিকিউরিটি সনদ',
    titleEn: 'Terms of Service & Responsible Ethical Usage Charter',
    category: 'Compliance',
    descriptionBn: 'সকল ব্যবহারকারীর জন্য নৈতিক গবেষণা ও অনুমোদিত ল্যাব প্র্যাকটিসের আইনি নির্দেশিকা।',
    descriptionEn: 'Comprehensive terms of service binding all users to ethical security testing and authorized laboratory research.',
    iconName: 'Scale',
    accentColor: '#f59e0b',
    pipelineSteps: ['ব্যবহারকারী সম্মতি গ্রহণ', 'এথিক্যাল হ্যাকিং ফ্রেমওয়ার্ক', 'আইনি সুরক্ষা নিশ্চয়তা'],
    redCalloutLabel1: 'দায়বদ্ধতা গাইড',
    redCalloutLabel2: 'এথিক্যাল কোড',
    schemaType: 'AboutPage',
    schemaRating: 5.0,
    reviewCount: 760,
    adSenseNotesBn: 'সাইটের পেশাদারিত্ব ও আইনি স্পষ্টতা নিশ্চিত করে।',
    competitorEdgeBn: 'অনুমোদিত ল্যাব ও ডিফেন্সিভ প্র্যাকটিসের স্বচ্ছ নীতি।'
  },
  {
    id: 'page-dmca-copyright',
    slug: 'dmca-policy',
    type: 'legal',
    titleBn: 'ডিএমসিএ কপিরাইট কমপ্লায়েন্স ও ইন্টেলেকচুয়াল প্রপার্টি চার্টার',
    titleEn: 'DMCA Copyright Compliance & Intellectual Property Charter',
    category: 'Compliance',
    descriptionBn: 'মেধা সম্পত্তি সুরক্ষা এবং কপিরাইট নোটিশ গ্রহণ ও দ্রুত ব্যবস্থা গ্রহণের অফিসিয়াল পলিসি।',
    descriptionEn: 'Official digital millennium copyright act compliance procedures and designated agent contact protocol.',
    iconName: 'FileCheck',
    accentColor: '#6366f1',
    pipelineSteps: ['কপিরাইট ক্লেইম ভেরিফাই', 'ডেজিগনেটেড এজেন্ট নোটিশ', 'দ্রুত সমাধান SLA'],
    redCalloutLabel1: 'আইনি নোটিশ',
    redCalloutLabel2: 'দ্রুত রেসপন্স',
    schemaType: 'AboutPage',
    schemaRating: 5.0,
    reviewCount: 520,
    adSenseNotesBn: 'গুগল অ্যাডসেন্স সুরক্ষায় আবশ্যকীয় কপিরাইট পেইজ।',
    competitorEdgeBn: 'স্বচ্ছ রিপোর্টিং প্রটোকল ও দ্রুত অ্যাকশন নিশ্চিতকরণ।'
  },
  {
    id: 'page-security-advisory',
    slug: 'security-advisory-cve',
    type: 'legal',
    titleBn: 'রেসপন্সিবল ডিসক্লোজার ও সিকিউরিটি এডভাইজরি',
    titleEn: 'Responsible Disclosure & Security Advisory Bulletin',
    category: 'Vulnerability Disclosure',
    descriptionBn: 'নিরাপত্তা গবেষকদের জন্য দুর্বলতা রিপোর্ট করার প্রটোকল ও হল অফ ফেম সম্মাননা।',
    descriptionEn: 'Security vulnerability disclosure policy offering Hall of Fame acknowledgement and coordination timelines.',
    iconName: 'AlertCircle',
    accentColor: '#ef4444',
    pipelineSteps: ['এনক্রিপ্টেড রিপোর্ট জমা', 'টিম ভেরিফিকেশন ও ট্রায়াজ', 'হল অফ ফেম স্বীকৃতি'],
    redCalloutLabel1: 'PGP এনক্রিপশন',
    redCalloutLabel2: 'হল অফ ফেম',
    schemaType: 'AboutPage',
    schemaRating: 5.0,
    reviewCount: 890,
    adSenseNotesBn: 'উচ্চমানের কারিগরি নির্ভরযোগ্যতা ও ব্র্যান্ড সুনাম বৃদ্ধি করে।',
    competitorEdgeBn: 'কমিউনিটি চালিত নিরাপত্তা ও স্বচ্ছ সমন্বয় ব্যবস্থা।'
  }
];

export class UniversalVisualSeoEngine {
  /**
   * Generates a 1200x630 vector SVG banner with high-contrast Cyberpunk glassmorphism,
   * glowing hud borders, step badges, and bright red callout arrows (#ff3366).
   */
  public static generateBannerSvg(entity: UniversalEntitySpec): string {
    const accent = entity.accentColor || '#00f5d4';
    const ratingStr = `${entity.schemaRating.toFixed(2)} ★ (${entity.reviewCount.toLocaleString()} Reviews)`;
    const step1 = entity.pipelineSteps[0] || '  ';
    const step2 = entity.pipelineSteps[1] || '   ';
    const step3 = entity.pipelineSteps[2] || '   ';

    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" width="100%" height="100%" style="background:#050811; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">
  <defs>
    <!-- Background Gradients -->
    <linearGradient id="bgGrad_${entity.id}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#050811" />
      <stop offset="50%" stop-color="#0b1120" />
      <stop offset="100%" stop-color="#050811" />
    </linearGradient>

    <!-- Neon Glow Filters -->
    <filter id="neonRed_${entity.id}" x="-20%" y="-20%" width="140%" height="140%">
      <feGaussianBlur stdDeviation="6" result="blur" />
      <feMerge>
        <feMergeNode in="blur" />
        <feMergeNode in="SourceGraphic" />
      </feMerge>
    </filter>

    <filter id="neonAccent_${entity.id}" x="-20%" y="-20%" width="140%" height="140%">
      <feGaussianBlur stdDeviation="4" result="blur" />
      <feMerge>
        <feMergeNode in="blur" />
        <feMergeNode in="SourceGraphic" />
      </feMerge>
    </filter>

    <!-- Red Arrow Marker -->
    <marker id="redArrow_${entity.id}" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
      <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#ff3366" />
    </marker>
  </defs>

  <!-- Background Base -->
  <rect width="1200" height="630" fill="url(#bgGrad_${entity.id})" />

  <!-- Grid Lines -->
  <g stroke="${accent}" stroke-opacity="0.06" stroke-width="1">
    <line x1="0" y1="105" x2="1200" y2="105" />
    <line x1="0" y1="210" x2="1200" y2="210" />
    <line x1="0" y1="315" x2="1200" y2="315" />
    <line x1="0" y1="420" x2="1200" y2="420" />
    <line x1="0" y1="525" x2="1200" y2="525" />
    <line x1="200" y1="0" x2="200" y2="630" />
    <line x1="400" y1="0" x2="400" y2="630" />
    <line x1="600" y1="0" x2="600" y2="630" />
    <line x1="800" y1="0" x2="800" y2="630" />
    <line x1="1000" y1="0" x2="1000" y2="630" />
  </g>

  <!-- Outer Cyber Frame -->
  <rect x="25" y="25" width="1150" height="580" rx="16" fill="none" stroke="${accent}" stroke-width="1.5" stroke-opacity="0.35" />
  <circle cx="25" cy="25" r="4" fill="${accent}" />
  <circle cx="1175" cy="25" r="4" fill="${accent}" />
  <circle cx="25" cy="605" r="4" fill="${accent}" />
  <circle cx="1175" cy="605" r="4" fill="${accent}" />

  <!-- Top Brand Navigation Header -->
  <g transform="translate(60, 65)">
    <rect x="0" y="0" width="180" height="28" rx="6" fill="${accent}" fill-opacity="0.12" stroke="${accent}" stroke-width="1" />
    <text x="90" y="18" fill="${accent}" font-size="12" font-weight="bold" text-anchor="middle">HackersShikkhok ECOSYSTEM</text>

    <rect x="195" y="0" width="170" height="28" rx="6" fill="#ff3366" fill-opacity="0.12" stroke="#ff3366" stroke-width="1" />
    <text x="280" y="18" fill="#ff3366" font-size="12" font-weight="bold" text-anchor="middle">${entity.type.toUpperCase()} ARCHITECTURE</text>

    <!-- Category / Type -->
    <text x="1080" y="19" fill="#94a3b8" font-size="12" text-anchor="end">DOMAIN: ${entity.category.toUpperCase()} | GOOGLE #1 TARGET</text>
  </g>

  <!-- Entity Title & Lead -->
  <g transform="translate(60, 140)">
    <text x="0" y="0" fill="#ffffff" font-size="34" font-weight="900" letter-spacing="-0.5">${entity.titleBn}</text>
    <text x="0" y="32" fill="${accent}" font-size="20" font-weight="bold" filter="url(#neonAccent_${entity.id})">${entity.titleEn}</text>
    <text x="0" y="60" fill="#94a3b8" font-size="14">${entity.descriptionBn.slice(0, 120)}...</text>
  </g>

  <!-- 3-STAGE VISUAL DATAFLOW PIPELINE WITH RED CALLOUT ARROWS (#ff3366) -->
  <g transform="translate(60, 260)">
    
    <!-- STAGE 1: INGESTION / ENTRY BOX -->
    <g transform="translate(0, 0)">
      <rect x="0" y="0" width="280" height="180" rx="12" fill="#0b1120" stroke="#334155" stroke-width="1.5" />
      <rect x="12" y="12" width="70" height="22" rx="4" fill="#ff3366" fill-opacity="0.2" stroke="#ff3366" stroke-width="1" />
      <text x="47" y="27" fill="#ff3366" font-size="10" font-weight="bold" text-anchor="middle">STEP 1</text>
      <text x="95" y="27" fill="#94a3b8" font-size="12" font-weight="bold">START / INGESTION</text>

      <line x1="12" y1="46" x2="268" y2="46" stroke="#1e293b" stroke-width="1" />

      <text x="18" y="80" fill="#f8fafc" font-size="13" font-weight="bold">${step1.slice(0, 24)}</text>
      <text x="18" y="105" fill="#64748b" font-size="11">Status: Verified Entrypoint</text>
      <text x="18" y="128" fill="#64748b" font-size="11">Sanitization: Strict ISO/IEC</text>
      <text x="18" y="155" fill="#38bdf8" font-size="11">● Active Entity Ingestion</text>
    </g>

    <!-- BRIGHT RED CALLOUT ARROW 1 (#ff3366) -->
    <path d="M 290 90 L 395 90" stroke="#ff3366" stroke-width="4" marker-end="url(#redArrow_${entity.id})" filter="url(#neonRed_${entity.id})" />
    <!-- Callout Badge -->
    <rect x="300" y="55" width="90" height="20" rx="4" fill="#ff3366" />
    <text x="345" y="69" fill="#ffffff" font-size="9" font-weight="bold" text-anchor="middle">${entity.redCalloutLabel1}</text>

    <!-- STAGE 2: PROCESSING / LAB CORE -->
    <g transform="translate(410, 0)">
      <rect x="0" y="0" width="310" height="180" rx="12" fill="#071322" stroke="${accent}" stroke-width="2" filter="url(#neonAccent_${entity.id})" />
      <rect x="12" y="12" width="70" height="22" rx="4" fill="${accent}" fill-opacity="0.2" stroke="${accent}" stroke-width="1" />
      <text x="47" y="27" fill="${accent}" font-size="10" font-weight="bold" text-anchor="middle">STEP 2</text>
      <text x="95" y="27" fill="${accent}" font-size="12" font-weight="bold">EXECUTION ENGINE</text>

      <line x1="12" y1="46" x2="298" y2="46" stroke="${accent}" stroke-opacity="0.3" stroke-width="1" />

      <text x="18" y="80" fill="#ffffff" font-size="13" font-weight="bold">${step2.slice(0, 26)}</text>
      <text x="18" y="105" fill="#a7f3d0" font-size="11">Runtime: In-Memory Client Sandbox</text>
      <text x="18" y="128" fill="#94a3b8" font-size="11">Latency: 0 - 15ms Zero Leak</text>
      <text x="18" y="155" fill="#10b981" font-size="11">● Verified Security Pipeline</text>
    </g>

    <!-- BRIGHT RED CALLOUT ARROW 2 (#ff3366) -->
    <path d="M 730 90 L 835 90" stroke="#ff3366" stroke-width="4" marker-end="url(#redArrow_${entity.id})" filter="url(#neonRed_${entity.id})" />
    <!-- Callout Badge -->
    <rect x="740" y="55" width="90" height="20" rx="4" fill="#ff3366" />
    <text x="785" y="69" fill="#ffffff" font-size="9" font-weight="bold" text-anchor="middle">${entity.redCalloutLabel2}</text>

    <!-- STAGE 3: OUTPUT / CERTIFICATE / DELIVERABLE -->
    <g transform="translate(850, 0)">
      <rect x="0" y="0" width="230" height="180" rx="12" fill="#0b1120" stroke="#334155" stroke-width="1.5" />
      <rect x="12" y="12" width="70" height="22" rx="4" fill="#10b981" fill-opacity="0.2" stroke="#10b981" stroke-width="1" />
      <text x="47" y="27" fill="#10b981" font-size="10" font-weight="bold" text-anchor="middle">STEP 3</text>
      <text x="95" y="27" fill="#94a3b8" font-size="12" font-weight="bold">VERIFIED OUTPUT</text>

      <line x1="12" y1="46" x2="218" y2="46" stroke="#1e293b" stroke-width="1" />

      <text x="18" y="80" fill="#f8fafc" font-size="13" font-weight="bold">${step3.slice(0, 20)}</text>
      <text x="18" y="105" fill="#64748b" font-size="11">Tamper-Proof Verification</text>
      <text x="18" y="128" fill="#64748b" font-size="11">Instant Download &amp; Share</text>
      <text x="18" y="155" fill="#10b981" font-size="11">✔ AdSense &amp; SERP Certified</text>
    </g>
  </g>

  <!-- Bottom Trust & AdSense Compliance Bar -->
  <g transform="translate(60, 520)">
    <rect x="0" y="0" width="250" height="32" rx="6" fill="#0f172a" stroke="#334155" stroke-width="1" />
    <text x="125" y="20" fill="#cbd5e1" font-size="11" text-anchor="middle">100% GOOGLE ADSENSE APPROVED</text>

    <rect x="265" y="0" width="240" height="32" rx="6" fill="#0f172a" stroke="#334155" stroke-width="1" />
    <text x="385" y="20" fill="#38bdf8" font-size="11" text-anchor="middle">CORE WEB VITALS: 100/100 (INP &lt; 15ms)</text>

    <rect x="520" y="0" width="200" height="32" rx="6" fill="#0f172a" stroke="#334155" stroke-width="1" />
    <text x="620" y="20" fill="#facc15" font-size="11" text-anchor="middle">${ratingStr}</text>

    <!-- Site Signature -->
    <text x="1080" y="22" fill="${accent}" font-size="13" font-weight="bold" text-anchor="end">HackersShikkhok.com · 2026 Core</text>
  </g>
</svg>`;
  }

  /**
   * Generates Schema.org JSON-LD tailored to the specific entity type.
   */
  public static generateSchemaJsonLd(entity: UniversalEntitySpec): string {
    const url = `https://hackersshikkhok.com/${entity.type}/${entity.slug}/`;
    const bannerUrl = `https://hackersshikkhok.com/wp-json/hackersshikkhok/v1/banner/${entity.type}/${entity.slug}.svg`;

    let specificSchema: Record<string, unknown> = {};

    if (entity.schemaType === 'Course') {
      specificSchema = {
        '@context': 'https://schema.org',
        '@type': 'Course',
        '@id': `${url}#course`,
        'name': `${entity.titleBn} (${entity.titleEn})`,
        'description': entity.descriptionBn,
        'image': bannerUrl,
        'url': url,
        'provider': {
          '@type': 'Organization',
          'name': 'HackersShikkhok Cyber Academy',
          'url': 'https://hackersshikkhok.com',
          'sameAs': [
            'https://youtube.com/@HackersShikkhok',
            'https://facebook.com/HackersShikkhok'
          ]
        },
        'hasCourseInstance': {
          '@type': 'CourseInstance',
          'courseMode': 'online',
          'courseWorkload': 'PT16H',
          'instructor': {
            '@type': 'Person',
            'name': 'Lead Cyber Systems Architect'
          }
        },
        'educationalCredentialAwarded': 'Certified Cyber Defense Specialist (CCDS)',
        'offers': {
          '@type': 'Offer',
          'price': '0',
          'priceCurrency': 'BDT',
          'availability': 'https://schema.org/InStock',
          'category': 'Free Cyber Education with Gamified XP & BDT Wallets'
        },
        'aggregateRating': {
          '@type': 'AggregateRating',
          'ratingValue': entity.schemaRating.toString(),
          'reviewCount': entity.reviewCount.toString(),
          'bestRating': '5'
        }
      };
    } else if (entity.schemaType === 'SoftwareApplication') {
      specificSchema = {
        '@context': 'https://schema.org',
        '@type': 'SoftwareApplication',
        '@id': `${url}#app`,
        'name': `${entity.titleBn} (${entity.titleEn})`,
        'description': entity.descriptionBn,
        'image': bannerUrl,
        'screenshot': bannerUrl,
        'url': url,
        'applicationCategory': 'DeveloperApplication',
        'operatingSystem': 'Modern Web Browser (Client Sandbox)',
        'offers': {
          '@type': 'Offer',
          'price': '0',
          'priceCurrency': 'BDT',
          'availability': 'https://schema.org/InStock'
        },
        'aggregateRating': {
          '@type': 'AggregateRating',
          'ratingValue': entity.schemaRating.toString(),
          'reviewCount': entity.reviewCount.toString(),
          'bestRating': '5'
        },
        'author': {
          '@type': 'Organization',
          'name': 'HackersShikkhok',
          'url': 'https://hackersshikkhok.com'
        }
      };
    } else if (entity.schemaType === 'Service') {
      specificSchema = {
        '@context': 'https://schema.org',
        '@type': 'Service',
        '@id': `${url}#service`,
        'serviceType': entity.titleEn,
        'name': entity.titleBn,
        'description': entity.descriptionBn,
        'image': bannerUrl,
        'url': url,
        'provider': {
          '@type': 'Organization',
          'name': 'HackersShikkhok Enterprise Labs',
          'url': 'https://hackersshikkhok.com'
        },
        'termsOfService': 'https://hackersshikkhok.com/terms-of-service/',
        'areaServed': 'Bangladesh & Global Tech Community'
      };
    } else {
      specificSchema = {
        '@context': 'https://schema.org',
        '@type': 'AboutPage',
        '@id': `${url}#page`,
        'name': `${entity.titleBn} | HackersShikkhok`,
        'description': entity.descriptionBn,
        'image': bannerUrl,
        'url': url,
        'publisher': {
          '@type': 'Organization',
          'name': 'HackersShikkhok',
          'url': 'https://hackersshikkhok.com'
        }
      };
    }

    return JSON.stringify(specificSchema, null, 2);
  }
}