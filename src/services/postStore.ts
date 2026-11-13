export interface UserPost {
  id: string;
  title: string;
  slug: string;
  type: 'tutorial' | 'device_review' | 'tech_news' | 'device_price' | 'code' | 'tools';
  category: string;
  status: 'publish' | 'draft' | 'pending';
  views: number;
  likes: number;
  commentsCount: number;
  createdAt: string;
  excerpt: string;
  content: string;
  tags: string[];
  specs?: {
    device?: string;
    display?: string;
    battery?: string;
    processor?: string;
    priceBdt?: string;
    pros?: string[];
    cons?: string[];
    rating?: number;
    source?: string;
  };
}

export const INITIAL_DEFAULT_POSTS: UserPost[] = [
  {
    id: 'post-1',
    title: 'Python ও Scapy দিয়ে অটোমেটেড নেটওয়ার্ক ট্র্যাফিক অ্যানালাইসিস এবং প্যাকেট স্নিফিং গাইড',
    slug: 'python-network-traffic-sniffing-guide',
    type: 'tutorial',
    category: 'Ethical Hacking & Python',
    status: 'publish',
    views: 14850,
    likes: 1240,
    commentsCount: 342,
    createdAt: '2026-10-18',
    excerpt: 'Scapy লাইব্রেরি ব্যবহার করে কীভাবে কাস্টম প্যাকেট তৈরি, হ্যান্ডশেক ট্র্যাকিং এবং ARP অ্যানোমালি সনাক্ত করবেন তার স্টেপ-বাই-স্টেপ গাইড।',
    content: 'Python একটি শক্তিশালী প্রোগ্রামিং ভাষা যা সাইবার সিকিউরিটি ও নেটওয়ার্ক অডিটে বহুল ব্যবহৃত হয়। Scapy লাইব্রেরির মাধ্যমে লেয়ার ২ থেকে লেয়ার ৭ পর্যন্ত সকল প্যাকেট ইনস্পেক্ট করা যায়। এই টিউটোরিয়ালে আমরা ARP Spoofing সনাক্তকরণ ও রিয়েল-টাইম প্যাকেট মনিটরিং স্ক্রিপ্ট তৈরি করেছি।',
    tags: ['Python', 'Scapy', 'Network Security', 'Wireshark']
  },
  {
    id: 'post-2',
    title: 'Samsung Galaxy A56 5G Review: ডিসপ্লে, পারফরম্যান্স, ক্যামেরা ও সিকিউরিটি টেস্ট রিপোর্ট',
    slug: 'samsung-galaxy-a56-review-bangladesh',
    type: 'device_review',
    category: 'Device Reviews',
    status: 'publish',
    views: 8940,
    likes: 910,
    commentsCount: 184,
    createdAt: '2026-10-17',
    excerpt: 'Samsung Galaxy A56 5G এর বাস্তব হ্যান্ডস-অন রিভিউ, Knox Vault হার্ডওয়্যার সিকিউরিটি বিশ্লেষণ এবং ভালো-মন্দের তুলনামূলক বিশ্লেষণ।',
    content: 'স্যামসাংয়ের জনপ্রিয় A-সিরিজের নতুন চমক Samsung Galaxy A56 5G। এতে রয়েছে সুপার অ্যামোলেড ১২০ হার্টজ রিফ্রেশ রেট ডিসপ্লে এবং স্যামসাংয়ের নিজস্ব Knox Vault সিকিউরিটি চিপ।',
    tags: ['Samsung', 'A56 5G', 'Smartphone Review', 'Knox Security'],
    specs: {
      device: 'Samsung Galaxy A56 5G',
      display: '6.6" Super AMOLED 120Hz FHD+',
      processor: 'Exynos 1580 (4nm) Octa-Core',
      battery: '5000 mAh, 45W Fast Charging',
      priceBdt: '৳48,500 (Official 8/128GB)',
      pros: ['দারুণ 120Hz ব্রাইট AMOLED ডিসপ্লে', 'Knox Vault হার্ডওয়্যার সিকিউরিটি আইসোলেশন', 'উন্নত ব্যাটারি ব্যাকআপ ও 45W ফাস্ট চার্জিং'],
      cons: ['বক্সে চার্জার দেওয়া হয় না', 'টেলিফটো লেন্স অনুপস্থিত'],
      rating: 9.1
    }
  },
  {
    id: 'post-3',
    title: 'স্যামসাংয়ের নতুন ফ্ল্যাগশিপ চিপসেট উন্মোচন: বাংলাদেশে লঞ্চের সম্ভাব্য তারিখ ও দাম',
    slug: 'samsung-new-flagship-chipset-launch-details',
    type: 'tech_news',
    category: 'Tech News',
    status: 'publish',
    views: 3420,
    likes: 420,
    commentsCount: 96,
    createdAt: '2026-10-16',
    excerpt: 'নতুন 3nm আর্কিটেকচারের চিপসেটের বিস্তারিত স্পেসিফিকেশন ও সিকিউরিটি ক্রিপ্টোগ্রাফিক ইঞ্জিন নিয়ে বিস্তারিত নিউজ আপডেট।',
    content: 'আন্তর্জাতিক টেক কনফারেন্সে স্যামসাং তাদের নতুন প্রসেসর প্রদর্শন করেছে। এতে থাকছে ডেডিকেটেড AI NPU যা লোকাল অন-ডিভাইস এনক্রিপশনে বিশেষ পারদর্শী।',
    tags: ['Tech News', 'Samsung', 'Chipset', 'Processor']
  },
  {
    id: 'post-4',
    title: 'Samsung Galaxy A56 Price in Bangladesh: অফিসিয়াল ও আনঅফিসিয়াল সকল ভ্যারিয়েন্টের দাম',
    slug: 'samsung-galaxy-a56-price-in-bangladesh',
    type: 'device_price',
    category: 'Device Prices',
    status: 'publish',
    views: 1240,
    likes: 550,
    commentsCount: 220,
    createdAt: '2026-10-15',
    excerpt: '৮/১২৮ জিবি এবং ১২/২৫৬ জিবি ভ্যারিয়েন্টের বর্তমান বাংলাদেশি বাজারমূল্য ও শোরুমের ওয়ারেন্টি বিবরণ।',
    content: 'বাংলাদেশের বাজারে Samsung Galaxy A56 এর বর্তমান অফিশিয়াল মূল্য ৳৪৮,৫০০ এবং আনঅফিসিয়াল আন্তর্জাতিক ভ্যারিয়েন্টের মূল্য আনুমানিক ৳৪২,০০০।',
    tags: ['Device Price', 'Samsung A56', 'Bangladesh Price', 'Specs'],
    specs: {
      device: 'Samsung Galaxy A56 5G',
      priceBdt: '৳48,500 (Official 8/128GB) / ৳54,000 (12/256GB)'
    }
  },
  {
    id: 'post-5',
    title: 'Linux x86-64 আর্কিটেকচারে অ্যাডভান্সড বাফার ওভারফ্লো এবং ROP চেইন তৈরি টিউটোরিয়াল',
    slug: 'buffer-overflow-rop-chain-exploitation',
    type: 'code',
    category: 'Binary Exploitation',
    status: 'draft',
    views: 0,
    likes: 0,
    commentsCount: 0,
    createdAt: '2026-10-19',
    excerpt: 'ASLR ও DEP প্রটেকশন বাইপাস করে কীভাবে Gadgets খুঁজে ROP চেইন গঠন করতে হয় তার খসড়া টিউটোরিয়াল।',
    content: 'এই গাইডে আমরা gdb-gef এবং ROPgadget টুলস ব্যবহার করে বাস্তব বাইনারি অ্যানালাইসিস করব। মেমরি করাপশন ও শ্যালকোড ক্রাফটিং এর প্রতিটি ধাপ এখানে কোডসহ ব্যাখ্যা করা হয়েছে।',
    tags: ['Exploit Dev', 'Buffer Overflow', 'ROP', 'Linux']
  },
  {
    id: 'post-6',
    title: 'WordPress REST API ও Custom Post Types সিকিউর করার ১০টি প্রো নিয়মাবলী',
    slug: 'secure-wordpress-rest-api-custom-endpoints',
    type: 'tutorial',
    category: 'WordPress Security',
    status: 'draft',
    views: 0,
    likes: 0,
    commentsCount: 0,
    createdAt: '2026-10-18',
    excerpt: 'Nonce ভেরিফিকেশন, Capability চেক এবং prepared statements সহ প্রোডাকশন গ্রেড প্লাগিন বানানোর গাইড।',
    content: 'ওয়ার্ডপ্রেস সিকিউরিটিতে REST API অ্যান্ডপয়েন্ট সুরক্ষিত রাখা অত্যন্ত গুরুত্বপূর্ণ। অনেকেই অনুমতি ছাড়াই sensitive ডেটা এক্সপোজ করে ফেলেন। এই আর্টিকেলে আমরা permission_callback ও Nonce ভেরিফিকেশন দিয়ে সিকিউর অ্যান্ডপয়েন্ট বানিয়েছি।',
    tags: ['WordPress', 'REST API', 'Security', 'PHP 8.2']
  },
  {
    id: 'post-7',
    title: 'Automated Subdomain Reconnaissance & Port Knocking Scanner Suite v2.0',
    slug: 'subdomain-recon-port-knocker-tool',
    type: 'tools',
    category: 'Security Tools',
    status: 'pending',
    views: 0,
    likes: 0,
    commentsCount: 0,
    createdAt: '2026-10-19',
    excerpt: 'মডুলার পাইথন স্ক্রিপ্ট যা DNS জোন ট্রান্সফার ও ব্রুটফোর্স রিকন দ্রুত সম্পন্ন করে। অ্যাডমিন রিভিউ পেন্ডিং।',
    content: 'টুলটি মাল্টিথ্রেডিং আর্কিটেকচারে প্রতি সেকেন্ডে ১০০+ সাবডোমেন রিজলভ করতে পারে এবং পোর্ট নকিং সিকোয়েন্স শনাক্ত করতে সহায়তা করে।',
    tags: ['Recon', 'Tools', 'Subdomain', 'Port Scanner']
  }
];

const STORAGE_KEY = 'hs_platform_posts_v4';
const listeners = new Set<() => void>();

function notifyListeners() {
  listeners.forEach((cb) => {
    try { cb(); } catch (e) { console.error('PostStore listener error:', e); }
  });
}

export class PostStore {
  public static getAll(): UserPost[] {
    if (typeof window === 'undefined') return INITIAL_DEFAULT_POSTS;
    try {
      const stored = localStorage.getItem(STORAGE_KEY);
      if (stored) {
        const parsed = JSON.parse(stored);
        if (Array.isArray(parsed) && parsed.length > 0) {
          return parsed;
        }
      }
    } catch (e) {
      console.warn('Failed to load posts from storage, using initial posts:', e);
    }
    // initialize storage
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(INITIAL_DEFAULT_POSTS));
    } catch (e) {}
    return INITIAL_DEFAULT_POSTS;
  }

  public static save(post: Partial<UserPost> & { title: string }): UserPost {
    const posts = this.getAll();
    const existingIndex = post.id ? posts.findIndex((p) => p.id === post.id) : -1;

    const slug = (post.slug || post.title)
      .toLowerCase()
      .trim()
      .replace(/[^\w\s-]/g, '')
      .replace(/[\s_-]+/g, '-')
      .replace(/^-+|-+$/g, '') || `post-${Date.now()}`;

    let savedPost: UserPost;

    if (existingIndex >= 0) {
      // Update existing post
      savedPost = {
        ...posts[existingIndex],
        ...post,
        title: post.title,
        slug,
        type: post.type || posts[existingIndex].type || 'tutorial',
        category: post.category || posts[existingIndex].category || 'General',
        status: post.status || posts[existingIndex].status || 'publish',
        content: post.content || posts[existingIndex].content || '',
        excerpt: post.excerpt || (post.content ? post.content.slice(0, 160) + '...' : posts[existingIndex].excerpt),
        tags: post.tags || posts[existingIndex].tags || ['CyberSecurity']
      };
      posts[existingIndex] = savedPost;
    } else {
      // Create new post
      savedPost = {
        id: post.id || `post-${Date.now()}`,
        title: post.title,
        slug,
        type: post.type || 'tutorial',
        category: post.category || 'General',
        status: post.status || 'publish',
        views: post.views !== undefined ? post.views : 1,
        likes: post.likes !== undefined ? post.likes : 1,
        commentsCount: post.commentsCount || 0,
        createdAt: post.createdAt || new Date().toISOString().slice(0, 10),
        excerpt: post.excerpt || (post.content ? post.content.slice(0, 160) + '...' : 'নতুন সাইবার ও প্রযুক্তি আর্টিকেল।'),
        content: post.content || 'বিস্তারিত কনটেন্ট খুব শীঘ্রই প্রকাশিত হবে...',
        tags: post.tags || ['CyberSecurity', 'Tutorial'],
        specs: post.specs
      };
      posts.unshift(savedPost);
    }

    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(posts));
    } catch (e) {
      console.error('Failed to save to localStorage:', e);
    }

    notifyListeners();
    return savedPost;
  }

  public static updateStatus(id: string, status: 'publish' | 'draft' | 'pending'): boolean {
    const posts = this.getAll();
    const index = posts.findIndex((p) => p.id === id);
    if (index === -1) return false;

    posts[index] = { ...posts[index], status };
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(posts));
    } catch (e) {}

    notifyListeners();
    return true;
  }

  public static delete(id: string): boolean {
    const posts = this.getAll();
    const filtered = posts.filter((p) => p.id !== id);
    if (filtered.length === posts.length) return false;

    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(filtered));
    } catch (e) {}

    notifyListeners();
    return true;
  }

  public static toggleLike(id: string): void {
    const posts = this.getAll();
    const index = posts.findIndex((p) => p.id === id);
    if (index === -1) return;

    posts[index] = {
      ...posts[index],
      likes: posts[index].likes + 1
    };

    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(posts));
    } catch (e) {}

    notifyListeners();
  }

  public static incrementViews(id: string): void {
    const posts = this.getAll();
    const index = posts.findIndex((p) => p.id === id);
    if (index === -1) return;

    posts[index] = {
      ...posts[index],
      views: posts[index].views + 1
    };

    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(posts));
    } catch (e) {}

    notifyListeners();
  }

  public static subscribe(listener: () => void): () => void {
    listeners.add(listener);
    return () => {
      listeners.delete(listener);
    };
  }
}
