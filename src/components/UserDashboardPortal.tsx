import React, { useState, useEffect } from 'react';
import { PostStore } from '../services/postStore';
import {
  FileText,
  PlusCircle,
  Eye,
  Heart,
  MessageSquare,
  Clock,
  CheckCircle2,
  AlertCircle,
  TrendingUp,
  Wallet,
  Award,
  Users,
  Search,
  Share2,
  Bookmark,
  Send,
  Trash2,
  Edit3,
  ExternalLink,
  ChevronRight,
  Filter,
  ArrowUpRight,
  Sparkles,
  Shield,
  Smartphone,
  DollarSign,
  Newspaper,
  Terminal,
  Layers,
  Check,
  X
} from 'lucide-react';

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

export interface QaQuestion {
  id: string;
  title: string;
  category: string;
  askedBy: string;
  askedAt: string;
  upvotes: number;
  answersCount: number;
  status: 'solved' | 'answered' | 'unanswered';
  acceptedAnswerId?: string;
  answers: Array<{
    id: string;
    answeredBy: string;
    authorRole: string;
    answeredAt: string;
    content: string;
    upvotes: number;
    isAccepted?: boolean;
  }>;
}

export interface WalletTransaction {
  id: string;
  date: string;
  type: 'credit' | 'debit';
  currency: 'BDT' | 'POINTS';
  amount: number;
  reason: string;
  status: 'completed' | 'processing';
}

export interface UserNotificationItem {
  id: string;
  title: string;
  detail: string;
  time: string;
  type: 'like' | 'comment' | 'answer' | 'system' | 'wallet';
  read: boolean;
  actionUrl?: string;
  targetPostId?: string;
  targetQuestionId?: string;
}

interface UserDashboardPortalProps {
  initialTab?: 'overview' | 'posts' | 'new-post' | 'qa-forum' | 'wallet' | 'notifications' | 'followers';
  initialStatusFilter?: 'all' | 'publish' | 'draft' | 'pending';
  walletBalance: number;
  walletPoints: number;
  onUpdateWallet: (newBalance: number, newPoints: number) => void;
  onNotify: (msg: string) => void;
  onOpenAppStudio?: () => void;
  onNavigateHome?: () => void;
}

export const UserDashboardPortal: React.FC<UserDashboardPortalProps> = ({
  initialTab = 'overview',
  initialStatusFilter = 'all',
  walletBalance,
  walletPoints,
  onUpdateWallet,
  onNotify,
  onOpenAppStudio,
  onNavigateHome
}) => {
  const [activeTab, setActiveTab] = useState<'overview' | 'posts' | 'new-post' | 'qa-forum' | 'wallet' | 'notifications' | 'followers'>(initialTab);
  const [statusFilter, setStatusFilter] = useState<'all' | 'publish' | 'draft' | 'pending'>(initialStatusFilter);
  const [typeFilter, setTypeFilter] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState('');

  // Current User Info
  const [userProfile] = useState({
    name: 'তানভীর হোসেন (Tanvir Hossain)',
    username: '@tanvir_cyber',
    email: 'tanvir@hackersshikkhok.com',
    role: 'Verified Author & Cyber Researcher',
    badge: 'Pro Elite Vanguard',
    avatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150&auto=format&fit=crop&q=80',
    joined: 'October 2024',
    bio: 'Full-stack cybersecurity engineer, ethical hacker and technical writer at HackersShikkhok. Specializing in Web Application Security, Reverse Engineering and WordPress Hardening.',
    division: 'ঢাকা বিভাগ (Dhaka Division)',
    district: 'ঢাকা (Dhaka District)',
    followersCount: 489,
    followingCount: 124
  });

  // User Posts synchronized with PostStore & localStorage
  const [posts, setPosts] = useState<UserPost[]>(() => PostStore.getAll());

  useEffect(() => {
    const unsub = PostStore.subscribe(() => {
      setPosts(PostStore.getAll());
    });
    return unsub;
  }, []);

  // Selected Post for View/Preview Modal
  const [selectedPostPreview, setSelectedPostPreview] = useState<UserPost | null>(null);

  // New Post Form State
  const [editingPostId, setEditingPostId] = useState<string | null>(null);
  const [postTitle, setPostTitle] = useState('');
  const [postType, setPostType] = useState<UserPost['type']>('tutorial');
  const [postCategory, setPostCategory] = useState('সাধারণ আর্টিকেল ও টিউটোরিয়াল');
  const [postContent, setPostContent] = useState('');
  const [postExcerpt, setPostExcerpt] = useState('');
  const [postTags, setPostTags] = useState('Python, Security, Guide');
  const [devicePrice, setDevicePrice] = useState('৳48,500');
  const [deviceDisplay, setDeviceDisplay] = useState('6.6" Super AMOLED 120Hz');
  const [deviceBattery, setDeviceBattery] = useState('5000 mAh 45W');
  const [deviceProcessor, setDeviceProcessor] = useState('Exynos 1580 (4nm)');
  const [devicePros, setDevicePros] = useState('উন্নত ১২০Hz ডিসপ্লে, ফাস্ট চার্জিং, সিকিউর চিপ');
  const [deviceCons, setDeviceCons] = useState('বক্সে চার্জার নেই');
  const [newsSource, setNewsSource] = useState('Samsung Global Newsroom');

  // Interactive Q&A Forum State
  const [qaQuestions, setQaQuestions] = useState<QaQuestion[]>([
    {
      id: 'qa-1',
      title: 'Python ও Scapy দিয়ে লোকাল নেটওয়ার্কে ARP Spoofing সনাক্ত ও প্রিভেন্ট করার আধুনিক পদ্ধতি কী?',
      category: 'Network Security',
      askedBy: 'তানভীর হোসেন (@tanvir_cyber)',
      askedAt: '২ ঘণ্টা আগে',
      upvotes: 42,
      answersCount: 2,
      status: 'solved',
      acceptedAnswerId: 'ans-1',
      answers: [
        {
          id: 'ans-1',
          answeredBy: '@shanto_dev',
          authorRole: 'Verified SOC Analyst · Elite Instructor',
          answeredAt: '১ ঘণ্টা আগে',
          content: 'ARP Spoofing সনাক্ত করার সবচেয়ে নির্ভরযোগ্য উপায় হলো লোকাল মেশিনের ARP ক্যাশ টেবিল মনিটর করা। Scapy দিয়ে `sniff(filter="arp", prn=process_arp_packet)` ফাংশন ব্যবহার করে যদি একই আইপি অ্যাড্রেস থেকে ২টি ভিন্ন MAC অ্যাড্রেসের রিপ্লাই (is-at) আসে, তাৎক্ষণিক অ্যালার্ট বা Dynamic ARP Inspection (DAI) সুইচ রুল ট্রিগার করতে পারেন। এছাড়া Arpwatch ডিরেক্টরি ইন্টিগ্রেশনও দারুণ কাজ করে।',
          upvotes: 28,
          isAccepted: true
        },
        {
          id: 'ans-2',
          answeredBy: '@cyber_hunter',
          authorRole: 'Ethical Hacker · OWASP Member',
          answeredAt: '৩০ মিনিট আগে',
          content: 'ল্যাপটপে স্ট্যাটিক ARP বাইন্ডিং `arp -s <Gateway-IP> <Gateway-MAC>` কমান্ড দিয়ে রাউটারের গেটওয়ে লক করে দেওয়া যায়। এতে করে আক্রমণকারী আপনার ট্র্যাফিক রি-রুট করতে পারবে না।',
          upvotes: 11,
          isAccepted: false
        }
      ]
    },
    {
      id: 'qa-2',
      title: 'WordPress এ REST API এন্ডপয়েন্টকে কীভাবে Nonce ও Capability দিয়ে সুরক্ষিত রাখা যায়?',
      category: 'WordPress Hardening',
      askedBy: 'তানভীর হোসেন (@tanvir_cyber)',
      askedAt: 'গতকাল',
      upvotes: 35,
      answersCount: 2,
      status: 'answered',
      answers: [
        {
          id: 'ans-3',
          answeredBy: '@wp_architect',
          authorRole: 'Lead WordPress Engineer',
          answeredAt: 'গতকাল রাত ৯:২০',
          content: 'রেজিস্টার করার সময় `permission_callback` প্যারামিটারে নিশ্চিত করুন: `current_user_can("edit_posts")` অথবা `wp_verify_nonce($request->get_header("X-WP-Nonce"), "wp_rest")`। কখনই `__return_true` দিয়ে পাবলিকলি ডাটাবেজ রাইট মেথড এক্সপোজ করবেন না।',
          upvotes: 19,
          isAccepted: false
        },
        {
          id: 'ans-4',
          answeredBy: '@arif_dev',
          authorRole: 'Full Stack Engineer',
          answeredAt: 'গতকাল রাত ১০:০৫',
          content: 'রেসপন্স স্যানিটাইজেশনে `rest_sanitize_value_from_schema` ব্যবহার করুন এবং ইনপুটকে অবশ্যই `$wpdb->prepare()` দিয়ে বাইন্ড করুন।',
          upvotes: 8,
          isAccepted: false
        }
      ]
    },
    {
      id: 'qa-3',
      title: 'Samsung Galaxy A56 5G এ কি ডেডিকেটেড Knox Vault হার্ডওয়্যার সিকিউরিটি চিপ রয়েছে?',
      category: 'Device Security',
      askedBy: 'তানভীর হোসেন (@tanvir_cyber)',
      askedAt: '৩ দিন আগে',
      upvotes: 18,
      answersCount: 1,
      status: 'answered',
      answers: [
        {
          id: 'ans-5',
          answeredBy: '@tech_reviewer_bd',
          authorRole: 'Senior Hardware Reviewer',
          answeredAt: '২ দিন আগে',
          content: 'হ্যাঁ! Samsung Galaxy A56 5G-তে স্যামসাং প্রথমবারের মতো মধ্যম বাজেটের ফোনে তাদের ফ্ল্যাগশিপ-গ্রেড Knox Vault সিকিউরিটি প্রসেসর যুক্ত করেছে যা পিন, পাসওয়ার্ড এবং বায়োমেট্রিক কি-স্টোরকে মূল প্রসেসরের মেমরি থেকে সম্পূর্ণ ফিজিক্যালি আলাদা রাখে।',
          upvotes: 14,
          isAccepted: false
        }
      ]
    }
  ]);

  // Selected Question for Full Expansion
  const [selectedQuestion, setSelectedQuestion] = useState<QaQuestion | null>(null);
  const [newAnswerText, setNewAnswerText] = useState('');
  const [isAskingQuestion, setIsAskingQuestion] = useState(false);
  const [askTitle, setAskTitle] = useState('');
  const [askCategory, setAskCategory] = useState('Network Security');
  const [askDetails, setAskDetails] = useState('');

  // Wallet Transactions History
  const [transactions, setTransactions] = useState<WalletTransaction[]>([
    {
      id: 'TXN-90214',
      date: '2026-10-19 18:30',
      type: 'credit',
      currency: 'BDT',
      amount: 15.00,
      reason: 'কন্টেন্ট পাবলিশ বোনাস (Python & Scapy Guide)',
      status: 'completed'
    },
    {
      id: 'TXN-90213',
      date: '2026-10-18 14:15',
      type: 'credit',
      currency: 'POINTS',
      amount: 50,
      reason: 'ফোরামে গৃহীত সেরা উত্তরের জন্য বোনাস পয়েন্ট',
      status: 'completed'
    },
    {
      id: 'TXN-90212',
      date: '2026-10-18 10:00',
      type: 'credit',
      currency: 'BDT',
      amount: 20.00,
      reason: 'Samsung Galaxy A56 রিভিউ আর্টিকেলে ১০০০+ ভিউ বোনাস',
      status: 'completed'
    },
    {
      id: 'TXN-90211',
      date: '2026-10-17 09:00',
      type: 'credit',
      currency: 'POINTS',
      amount: 25,
      reason: 'দৈনিক সাইট লগইন ও লার্নিং চেকপয়েন্ট',
      status: 'completed'
    },
    {
      id: 'TXN-90210',
      date: '2026-10-16 21:40',
      type: 'debit',
      currency: 'BDT',
      amount: 50.00,
      reason: 'বিকাশ ক্যাশআউট রিকোয়েস্ট (01712***890)',
      status: 'completed'
    }
  ]);

  // Withdrawal Form State
  const [withdrawMethod, setWithdrawMethod] = useState<'bKash' | 'Nagad' | 'Rocket'>('bKash');
  const [withdrawAccount, setWithdrawAccount] = useState('01712345678');
  const [withdrawAmount, setWithdrawAmount] = useState('50');

  // Real-time Actionable Notifications
  const [userNotifications, setUserNotifications] = useState<UserNotificationItem[]>([
    {
      id: 'notif-1',
      title: 'নতুন লাইক ও এপ্রিশিয়েশন ❤️',
      detail: 'ব্যবহারকারী @shanto_dev আপনার "Python ও Scapy" পোস্টে লাইক দিয়েছেন।',
      time: '১০ মিনিট আগে',
      type: 'like',
      read: false,
      targetPostId: 'post-1'
    },
    {
      id: 'notif-2',
      title: 'প্রশ্নে নতুন উত্তর এসেছে 💬',
      detail: 'আপনার "Python দিয়ে ARP Spoofing সনাক্তকরণ" প্রশ্নে @shanto_dev বিস্তারিত উত্তর দিয়েছেন।',
      time: '১ ঘণ্টা আগে',
      type: 'answer',
      read: false,
      targetQuestionId: 'qa-1'
    },
    {
      id: 'notif-3',
      title: 'নতুন ফলোয়ার 👥',
      detail: '@hacker_arif আপনাকে ফলো করা শুরু করেছেন।',
      time: '৩ ঘণ্টা আগে',
      type: 'system',
      read: false
    },
    {
      id: 'notif-4',
      title: 'ওয়ালেটে টাকা যোগ হয়েছে 💰',
      detail: 'আপনার "Samsung Galaxy A56 রিভিউ" পোস্টে মাইলফলক অর্জনের জন্য ৳২০.০০ যোগ হয়েছে।',
      time: 'গতকাল',
      type: 'wallet',
      read: true
    },
    {
      id: 'notif-5',
      title: 'নতুন কমেন্ট 💬',
      detail: 'ব্যবহারকারী @cyber_rahat আপনার Samsung প্রাইস আর্টিকেলে কমেন্ট করেছেন: "৮/১২৮ জিবি কি বসুন্ধরা সিটিতে এভেইলেবল?"',
      time: 'গতকাল',
      type: 'comment',
      read: true,
      targetPostId: 'post-4'
    }
  ]);

  // Followers List
  const [followers, setFollowers] = useState([
    { id: 'f-1', name: 'আব্দুল কাদের শান্ত (Shanto)', handle: '@shanto_dev', role: 'SOC Analyst · Instructor', avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80', isFollowingBack: true },
    { id: 'f-2', name: 'আরিফুল ইসলাম (Arif)', handle: '@hacker_arif', role: 'Bug Bounty Hunter · OWASP', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80', isFollowingBack: true },
    { id: 'f-3', name: 'সুমাইয়া রহমান (Sumaiya)', handle: '@sumaiya_cyber', role: 'Malware Researcher', avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80', isFollowingBack: false },
    { id: 'f-4', name: 'রাহাত আহমেদ (Rahat)', handle: '@cyber_rahat', role: 'Full Stack Developer', avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&auto=format&fit=crop&q=80', isFollowingBack: false }
  ]);

  // Metrics Calculation
  const totalViews = posts.reduce((sum, p) => sum + p.views, 0);
  const totalLikes = posts.reduce((sum, p) => sum + p.likes, 0);
  const totalComments = posts.reduce((sum, p) => sum + p.commentsCount, 0);
  const publishedCount = posts.filter((p) => p.status === 'publish').length;
  const draftCount = posts.filter((p) => p.status === 'draft').length;
  const pendingCount = posts.filter((p) => p.status === 'pending').length;
  const unreadNotifCount = userNotifications.filter((n) => !n.read).length;

  // Filtered Posts
  const filteredPosts = posts.filter((p) => {
    const matchesStatus = statusFilter === 'all' || p.status === statusFilter;
    const matchesType = typeFilter === 'all' || p.type === typeFilter;
    const matchesSearch =
      !searchQuery.trim() ||
      p.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
      p.tags.some((t) => t.toLowerCase().includes(searchQuery.toLowerCase())) ||
      p.category.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesStatus && matchesType && matchesSearch;
  });

  // Handle Save / Publish Post
  const handleSavePost = (targetStatus: 'publish' | 'draft' | 'pending') => {
    if (!postTitle.trim()) {
      onNotify('দয়া করে আর্টিকেলের একটি আকর্ষণীয় শিরোনাম লিখুন।');
      return;
    }

    const saved = PostStore.save({
      id: editingPostId || undefined,
      title: postTitle,
      type: postType,
      category: postCategory,
      status: targetStatus,
      excerpt: postExcerpt || postContent.slice(0, 150) + '...',
      content: postContent,
      tags: postTags.split(',').map((t) => t.trim()).filter(Boolean),
      specs:
        postType === 'device_review' || postType === 'device_price'
          ? {
              device: postTitle,
              display: deviceDisplay,
              battery: deviceBattery,
              processor: deviceProcessor,
              priceBdt: devicePrice,
              pros: devicePros.split(',').map((p) => p.trim()),
              cons: deviceCons.split(',').map((c) => c.trim()),
              rating: 9.0
            }
          : undefined
    });

    if (editingPostId) {
      onNotify(`পোস্ট "${postTitle}" সফলভাবে আপডেট করা হয়েছে (${targetStatus})!`);
    } else {
      if (targetStatus === 'publish') {
        const rewardBdt = 15.0;
        const rewardPoints = 50;
        onUpdateWallet(walletBalance + rewardBdt, walletPoints + rewardPoints);

        const newTxn: WalletTransaction = {
          id: `TXN-${Date.now().toString().slice(-5)}`,
          date: new Date().toLocaleString(),
          type: 'credit',
          currency: 'BDT',
          amount: rewardBdt,
          reason: `কন্টেন্ট সরাসরি পাবলিশ বোনাস: ${postTitle.slice(0, 30)}...`,
          status: 'completed'
        };
        setTransactions((prev) => [newTxn, ...prev]);

        setUserNotifications((prev) => [
          {
            id: `notif-${Date.now()}`,
            title: 'পোস্ট পাবলিশ সফল ও রিওয়ার্ড প্রাপ্তি! 🚀',
            detail: `আপনার আর্টিকেল সফলভাবে লাইভ হয়েছে এবং ওয়ালেটে +৳${rewardBdt.toFixed(2)} ও +${rewardPoints} XP যোগ হয়েছে।`,
            time: 'এইমাত্র',
            type: 'wallet',
            read: false,
            targetPostId: saved.id
          },
          ...prev
        ]);

        onNotify(`🎉 চমৎকার! আপনার পোস্ট "${postTitle}" লাইভ হয়েছে এবং ৳${rewardBdt} ও ${rewardPoints} XP যুক্ত হয়েছে!`);
      } else if (targetStatus === 'draft') {
        onNotify(`পোস্ট "${postTitle}" ড্রাফট (Draft) হিসেবে নিরাপদে সংরক্ষিত হয়েছে।`);
      } else {
        onNotify(`পোস্ট "${postTitle}" অ্যাডমিন পেন্ডিং রিভিউতে পাঠানো হয়েছে। অনুমোদনের পর লাইভ হবে।`);
      }
    }

    // Reset Form & Switch to posts tab so user immediately sees their created/updated post!
    setEditingPostId(null);
    setPostTitle('');
    setPostContent('');
    setPostExcerpt('');
    setActiveTab('posts');
    setStatusFilter(targetStatus);
  };

  // Delete Post
  const handleDeletePost = (id: string, title: string) => {
    if (confirm(`আপনি কি নিশ্চিত যে "${title}" পোস্টটি মুছে ফেলতে চান?`)) {
      PostStore.delete(id);
      onNotify(`পোস্ট "${title}" সফলভাবে ডিলিট করা হয়েছে।`);
    }
  };

  // Pre-load Post for Editing
  const handleStartEdit = (post: UserPost) => {
    setEditingPostId(post.id);
    setPostTitle(post.title);
    setPostType(post.type);
    setPostCategory(post.category);
    setPostContent(post.content);
    setPostExcerpt(post.excerpt);
    setPostTags(post.tags.join(', '));
    if (post.specs) {
      setDevicePrice(post.specs.priceBdt || '৳48,500');
      setDeviceDisplay(post.specs.display || '6.6" Super AMOLED 120Hz');
      setDeviceBattery(post.specs.battery || '5000 mAh 45W');
      setDeviceProcessor(post.specs.processor || 'Exynos 1580');
      setDevicePros(post.specs.pros?.join(', ') || '');
      setDeviceCons(post.specs.cons?.join(', ') || '');
    }
    setActiveTab('new-post');
  };

  // Handle Q&A Reply
  const handleAddAnswer = (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedQuestion || !newAnswerText.trim()) return;

    const newAns = {
      id: `ans-${Date.now()}`,
      answeredBy: userProfile.username,
      authorRole: 'Verified Author · Community Contributor',
      answeredAt: 'এইমাত্র',
      content: newAnswerText.trim(),
      upvotes: 0,
      isAccepted: false
    };

    setQaQuestions((prev) =>
      prev.map((q) =>
        q.id === selectedQuestion.id
          ? {
              ...q,
              answersCount: q.answersCount + 1,
              status: q.status === 'unanswered' ? 'answered' : q.status,
              answers: [...q.answers, newAns]
            }
          : q
      )
    );

    setSelectedQuestion((prev) =>
      prev
        ? {
            ...prev,
            answersCount: prev.answersCount + 1,
            answers: [...prev.answers, newAns]
          }
        : null
    );

    setNewAnswerText('');
    onUpdateWallet(walletBalance, walletPoints + 15);
    onNotify('আপনার উত্তরটি সফলভাবে যোগ করা হয়েছে এবং +১৫ XP অর্জন করেছেন!');
  };

  // Ask New Question
  const handleAskQuestion = (e: React.FormEvent) => {
    e.preventDefault();
    if (!askTitle.trim()) return;

    const newQ: QaQuestion = {
      id: `qa-${Date.now()}`,
      title: askTitle,
      category: askCategory,
      askedBy: userProfile.name,
      askedAt: 'এইমাত্র',
      upvotes: 1,
      answersCount: 0,
      status: 'unanswered',
      answers: []
    };

    setQaQuestions((prev) => [newQ, ...prev]);
    setIsAskingQuestion(false);
    setAskTitle('');
    setAskDetails('');
    onUpdateWallet(walletBalance, walletPoints + 10);
    onNotify('আপনার প্রশ্নটি ফোরামে পোস্ট করা হয়েছে (+১০ XP)। এক্সপার্ট উত্তর শীঘ্রই পাবেন!');
  };

  // Withdraw Money
  const handleWithdrawRequest = (e: React.FormEvent) => {
    e.preventDefault();
    const amt = parseFloat(withdrawAmount);
    if (isNaN(amt) || amt <= 0) {
      onNotify('দয়া করে সঠিক উইথড্রয়াল অ্যামাউন্ট লিখুন।');
      return;
    }
    if (amt > walletBalance) {
      onNotify(`অপর্যাপ্ত ব্যালেন্স! আপনার বর্তমান ব্যালেন্স ৳${walletBalance.toFixed(2)}`);
      return;
    }

    const newBal = walletBalance - amt;
    onUpdateWallet(newBal, walletPoints);

    const newTxn: WalletTransaction = {
      id: `TXN-${Date.now().toString().slice(-5)}`,
      date: new Date().toLocaleString(),
      type: 'debit',
      currency: 'BDT',
      amount: amt,
      reason: `${withdrawMethod} উইথড্রয়াল রিকোয়েস্ট (${withdrawAccount})`,
      status: 'completed'
    };
    setTransactions((prev) => [newTxn, ...prev]);

    setUserNotifications((prev) => [
      {
        id: `notif-${Date.now()}`,
        title: 'উইথড্রয়াল প্রসেসিং 💸',
        detail: `আপনার ৳${amt.toFixed(2)} টাকা ${withdrawMethod} একাউন্টে (${withdrawAccount}) প্রেরণের রিকোয়েস্ট গৃহীত হয়েছে।`,
        time: 'এইমাত্র',
        type: 'wallet',
        read: false
      },
      ...prev
    ]);

    onNotify(`৳${amt} টাকা ${withdrawMethod}-এ প্রেরণের রিকোয়েস্ট সম্পন্ন হয়েছে!`);
  };

  return (
    <div className="w-full space-y-6">
      {/* =====================================================================
          1. USER PROFILE HEADER & QUICK ANALYTICS CARDS
      ====================================================================== */}
      <div className="rounded-2xl bg-gradient-to-br from-[#0b1120] via-[#0f172a] to-[#050811] border border-[#00f5d4]/30 p-6 md:p-8 shadow-[0_0_40px_rgba(0,245,212,0.08)]">
        <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
          {/* User Identity & Avatar */}
          <div className="flex items-start sm:items-center gap-4">
            <div className="relative">
              <img
                src={userProfile.avatar}
                alt={userProfile.name}
                className="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover border-2 border-[#00f5d4] shadow-[0_0_20px_rgba(0,245,212,0.4)]"
              />
              <span className="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-[#0b1120] flex items-center justify-center text-[10px] text-white" title="Active Online">
                ✓
              </span>
            </div>

            <div className="space-y-1">
              <div className="flex flex-wrap items-center gap-2">
                <h1 className="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                  {userProfile.name}
                </h1>
                <span className="px-2.5 py-0.5 rounded-full bg-[#00f5d4]/15 border border-[#00f5d4]/40 text-[#00f5d4] text-xs font-mono font-bold flex items-center gap-1">
                  <Shield className="w-3 h-3" />
                  {userProfile.badge}
                </span>
              </div>

              <div className="text-xs sm:text-sm font-mono text-slate-300 flex flex-wrap items-center gap-3">
                <span className="text-[#00f5d4]">{userProfile.username}</span>
                <span>•</span>
                <span>{userProfile.role}</span>
                <span>•</span>
                <span className="text-slate-400">📍 {userProfile.district}</span>
              </div>

              <p className="text-xs text-slate-400 max-w-2xl leading-relaxed pt-1">
                {userProfile.bio}
              </p>
            </div>
          </div>

          {/* Header Action Buttons */}
          <div className="flex flex-wrap items-center gap-3 shrink-0">
            <button
              onClick={() => {
                setEditingPostId(null);
                setPostTitle('');
                setPostContent('');
                setActiveTab('new-post');
              }}
              className="px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#00f5d4] to-emerald-500 hover:opacity-95 text-[#050811] font-extrabold text-xs sm:text-sm flex items-center gap-2 shadow-[0_0_20px_rgba(0,245,212,0.35)] cursor-pointer"
            >
              <PlusCircle className="w-4 h-4" />
              <span>📝 অ্যাড নিউ পোস্ট (Add Post)</span>
            </button>

            <button
              onClick={() => {
                setActiveTab('qa-forum');
                setIsAskingQuestion(true);
              }}
              className="px-4 py-2.5 rounded-xl bg-[#7c3aed]/20 hover:bg-[#7c3aed]/30 border border-[#7c3aed]/50 text-purple-300 font-bold text-xs sm:text-sm flex items-center gap-2 cursor-pointer"
            >
              <MessageSquare className="w-4 h-4" />
              <span>❓ প্রশ্ন করুন (Ask Q&amp;A)</span>
            </button>

            {onNavigateHome && (
              <button
                onClick={onNavigateHome}
                className="px-3.5 py-2.5 rounded-xl bg-[#050811] hover:bg-slate-900 border border-slate-700 text-slate-300 text-xs font-semibold cursor-pointer"
              >
                🏠 হোমপেজ
              </button>
            )}
          </div>
        </div>

        {/* 6 Key Performance Metrics Cards */}
        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 pt-6 mt-6 border-t border-slate-800">
          <div className="p-3.5 rounded-xl bg-[#050811]/90 border border-slate-800">
            <div className="flex items-center justify-between text-slate-400 text-xs">
              <span>মোট ভিউজ</span>
              <Eye className="w-3.5 h-3.5 text-cyan-400" />
            </div>
            <div className="text-xl font-extrabold font-mono text-cyan-400 mt-1">
              {totalViews.toLocaleString()}
            </div>
            <div className="text-[11px] text-slate-400 mt-0.5">সবগুলো পোস্টে মোট ট্র্যাফিক</div>
          </div>

          <div className="p-3.5 rounded-xl bg-[#050811]/90 border border-slate-800">
            <div className="flex items-center justify-between text-slate-400 text-xs">
              <span>মোট লাইক</span>
              <Heart className="w-3.5 h-3.5 text-rose-400" />
            </div>
            <div className="text-xl font-extrabold font-mono text-rose-400 mt-1">
              {totalLikes.toLocaleString()}
            </div>
            <div className="text-[11px] text-slate-400 mt-0.5">পাঠকদের এপ্রিশিয়েশন</div>
          </div>

          <div className="p-3.5 rounded-xl bg-[#050811]/90 border border-slate-800">
            <div className="flex items-center justify-between text-slate-400 text-xs">
              <span>মোট কমেন্টস</span>
              <MessageSquare className="w-3.5 h-3.5 text-purple-400" />
            </div>
            <div className="text-xl font-extrabold font-mono text-purple-400 mt-1">
              {totalComments.toLocaleString()}
            </div>
            <div className="text-[11px] text-slate-400 mt-0.5">অ্যাক্টিভ ডিসকাশন</div>
          </div>

          <div className="p-3.5 rounded-xl bg-[#050811]/90 border border-slate-800">
            <div className="flex items-center justify-between text-slate-400 text-xs">
              <span>ফলোয়ার্স</span>
              <Users className="w-3.5 h-3.5 text-indigo-400" />
            </div>
            <div className="text-xl font-extrabold font-mono text-indigo-400 mt-1">
              {userProfile.followersCount}
            </div>
            <div className="text-[11px] text-slate-400 mt-0.5">{userProfile.followingCount} ফলো করছেন</div>
          </div>

          <div className="p-3.5 rounded-xl bg-[#050811]/90 border border-[#00f5d4]/40 bg-gradient-to-b from-[#00f5d4]/5 to-transparent">
            <div className="flex items-center justify-between text-slate-400 text-xs">
              <span>ক্যাশ ওয়ালেট</span>
              <Wallet className="w-3.5 h-3.5 text-[#00f5d4]" />
            </div>
            <div className="text-xl font-extrabold font-mono text-[#00f5d4] mt-1">
              ৳{walletBalance.toFixed(2)}
            </div>
            <div className="text-[11px] text-emerald-400 mt-0.5">উইথড্র যোগ্য ব্যালেন্স</div>
          </div>

          <div className="p-3.5 rounded-xl bg-[#050811]/90 border border-amber-500/40 bg-gradient-to-b from-amber-500/5 to-transparent">
            <div className="flex items-center justify-between text-slate-400 text-xs">
              <span>লার্নিং পয়েন্ট</span>
              <Award className="w-3.5 h-3.5 text-amber-400" />
            </div>
            <div className="text-xl font-extrabold font-mono text-amber-400 mt-1">
              {walletPoints} XP
            </div>
            <div className="text-[11px] text-amber-300 mt-0.5">লেভেল ৪ ভেটেরান</div>
          </div>
        </div>
      </div>

      {/* =====================================================================
          2. DASHBOARD TAB NAVIGATION BAR (SUB-NAV)
      ====================================================================== */}
      <div className="flex flex-wrap items-center justify-between gap-3 bg-[#0b1120] border border-slate-800 rounded-xl p-2 sticky top-[72px] z-30 backdrop-blur-md">
        <div className="flex flex-wrap items-center gap-1.5 sm:gap-2">
          <button
            onClick={() => setActiveTab('overview')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              activeTab === 'overview'
                ? 'bg-[#00f5d4] text-[#050811] shadow-[0_0_15px_rgba(0,245,212,0.3)]'
                : 'text-slate-300 hover:text-white hover:bg-slate-800'
            }`}
          >
            <TrendingUp className="w-3.5 h-3.5" />
            <span>ওভারভিউ (Overview)</span>
          </button>

          <button
            onClick={() => {
              setActiveTab('posts');
              setStatusFilter('all');
            }}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              activeTab === 'posts' && statusFilter === 'all'
                ? 'bg-[#00f5d4] text-[#050811]'
                : 'text-slate-300 hover:text-white hover:bg-slate-800'
            }`}
          >
            <FileText className="w-3.5 h-3.5" />
            <span>আমার কন্টেন্ট ({posts.length})</span>
          </button>

          <button
            onClick={() => {
              setActiveTab('posts');
              setStatusFilter('publish');
            }}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              activeTab === 'posts' && statusFilter === 'publish'
                ? 'bg-emerald-500 text-white'
                : 'text-slate-300 hover:text-white hover:bg-slate-800'
            }`}
          >
            <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" />
            <span>পাবলিশড ({publishedCount})</span>
          </button>

          <button
            onClick={() => {
              setActiveTab('posts');
              setStatusFilter('draft');
            }}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              activeTab === 'posts' && statusFilter === 'draft'
                ? 'bg-slate-200 text-slate-900'
                : 'text-slate-300 hover:text-white hover:bg-slate-800'
            }`}
          >
            <Clock className="w-3.5 h-3.5 text-slate-400" />
            <span>ড্রাফট ({draftCount})</span>
          </button>

          <button
            onClick={() => {
              setActiveTab('posts');
              setStatusFilter('pending');
            }}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              activeTab === 'posts' && statusFilter === 'pending'
                ? 'bg-amber-500 text-slate-900'
                : 'text-slate-300 hover:text-white hover:bg-slate-800'
            }`}
          >
            <AlertCircle className="w-3.5 h-3.5 text-amber-400" />
            <span>পেন্ডিং ({pendingCount})</span>
          </button>

          <button
            onClick={() => setActiveTab('new-post')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              activeTab === 'new-post'
                ? 'bg-purple-600 text-white shadow-[0_0_15px_rgba(168,85,247,0.3)]'
                : 'text-purple-300 hover:bg-purple-900/30'
            }`}
          >
            <PlusCircle className="w-3.5 h-3.5" />
            <span>পোস্ট লিখুন</span>
          </button>

          <button
            onClick={() => setActiveTab('qa-forum')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              activeTab === 'qa-forum'
                ? 'bg-cyan-500 text-[#050811] font-extrabold'
                : 'text-slate-300 hover:text-white hover:bg-slate-800'
            }`}
          >
            <MessageSquare className="w-3.5 h-3.5" />
            <span>প্রশ্নোত্তর ফোরাম ({qaQuestions.length})</span>
          </button>

          <button
            onClick={() => setActiveTab('wallet')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 ${
              activeTab === 'wallet'
                ? 'bg-[#00f5d4] text-[#050811]'
                : 'text-slate-300 hover:text-white hover:bg-slate-800'
            }`}
          >
            <Wallet className="w-3.5 h-3.5" />
            <span>লেনদেন ও ক্যাশআউট</span>
          </button>

          <button
            onClick={() => setActiveTab('notifications')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 relative ${
              activeTab === 'notifications'
                ? 'bg-rose-500 text-white'
                : 'text-slate-300 hover:text-white hover:bg-slate-800'
            }`}
          >
            <span>🔔 নোটিফিকেশন</span>
            {unreadNotifCount > 0 && (
              <span className="w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] flex items-center justify-center font-bold">
                {unreadNotifCount}
              </span>
            )}
          </button>
        </div>

        {/* Search inside user's posts & questions */}
        <div className="relative w-full sm:w-64">
          <Search className="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="কন্টেন্ট বা প্রশ্ন খুঁজুন..."
            className="w-full pl-8 pr-3 py-1 rounded-lg bg-[#050811] border border-slate-800 text-xs text-white focus:outline-none focus:border-[#00f5d4]"
          />
        </div>
      </div>

      {/* =====================================================================
          3. TAB 1: OVERVIEW & ENGAGEMENT ANALYTICS
      ====================================================================== */}
      {activeTab === 'overview' && (
        <div className="space-y-6">
          {/* Quick Actions & Author Level Progression */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div className="lg:col-span-2 rounded-2xl bg-[#0b1120] border border-slate-800 p-6 space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <h3 className="text-base font-bold text-white flex items-center gap-2">
                    <Sparkles className="w-4 h-4 text-[#00f5d4]" />
                    <span>কন্টেন্ট পারফরম্যান্স ও সাম্প্রতিক প্রকাশনা</span>
                  </h3>
                  <p className="text-xs text-slate-400 mt-0.5">
                    আপনার প্রকাশিত আর্টিকেলের লাইভ রেটিং ও অ্যানালিটিক্স
                  </p>
                </div>
                <button
                  onClick={() => {
                    setActiveTab('posts');
                    setStatusFilter('all');
                  }}
                  className="text-xs text-[#00f5d4] hover:underline font-mono flex items-center gap-1 cursor-pointer"
                >
                  সবগুলো দেখুন ({posts.length}) <ChevronRight className="w-3 h-3" />
                </button>
              </div>

              {/* List of top 3 performing articles */}
              <div className="space-y-3">
                {posts.slice(0, 3).map((post) => (
                  <div
                    key={post.id}
                    className="p-4 rounded-xl bg-[#050811] border border-slate-800/80 hover:border-slate-700 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                  >
                    <div className="space-y-1">
                      <div className="flex items-center gap-2">
                        <span
                          className={`px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase ${
                            post.type === 'tutorial'
                              ? 'bg-blue-500/20 text-blue-300'
                              : post.type === 'device_review'
                              ? 'bg-purple-500/20 text-purple-300'
                              : post.type === 'tech_news'
                              ? 'bg-emerald-500/20 text-emerald-300'
                              : 'bg-amber-500/20 text-amber-300'
                          }`}
                        >
                          {post.type.replace('_', ' ')}
                        </span>
                        <span className="text-xs text-slate-400">{post.createdAt}</span>
                      </div>
                      <h4 className="text-sm font-bold text-white hover:text-[#00f5d4] transition-colors cursor-pointer" onClick={() => setSelectedPostPreview(post)}>
                        {post.title}
                      </h4>
                    </div>

                    <div className="flex items-center gap-4 text-xs font-mono shrink-0">
                      <span className="text-cyan-400 flex items-center gap-1">
                        <Eye className="w-3.5 h-3.5" />
                        {post.views.toLocaleString()}
                      </span>
                      <span className="text-rose-400 flex items-center gap-1">
                        <Heart className="w-3.5 h-3.5" />
                        {post.likes}
                      </span>
                      <span className="text-purple-400 flex items-center gap-1">
                        <MessageSquare className="w-3.5 h-3.5" />
                        {post.commentsCount}
                      </span>
                      <button
                        onClick={() => setSelectedPostPreview(post)}
                        className="px-2.5 py-1 rounded bg-[#00f5d4]/15 hover:bg-[#00f5d4]/25 text-[#00f5d4] text-[11px] font-bold cursor-pointer"
                      >
                        ভিউ প্রিভিউ
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Author Level & Gamified Progress */}
            <div className="rounded-2xl bg-[#0b1120] border border-slate-800 p-6 flex flex-col justify-between space-y-4">
              <div>
                <div className="flex items-center justify-between">
                  <span className="text-xs font-mono text-[#00f5d4] font-bold">AUTHOR RANK TIER</span>
                  <span className="text-xs font-mono text-amber-400 font-bold">{walletPoints} / 1000 XP</span>
                </div>
                <h3 className="text-lg font-bold text-white mt-1">Level 4: Cyber Vanguard</h3>
                <p className="text-xs text-slate-400 mt-1">
                  আর মাত্র ১৫০ XP অর্জন করলে আপনি Level 5: Security Architect ব্যাজ আনলক করবেন।
                </p>

                {/* Progress bar */}
                <div className="w-full bg-[#050811] h-3 rounded-full mt-3 overflow-hidden p-0.5 border border-slate-800">
                  <div
                    className="bg-gradient-to-r from-[#00f5d4] via-emerald-400 to-amber-400 h-full rounded-full transition-all duration-500"
                    style={{ width: `${Math.min(100, (walletPoints / 1000) * 100)}%` }}
                  ></div>
                </div>
              </div>

              {/* Perks List */}
              <div className="space-y-2 border-t border-slate-800 pt-4 text-xs">
                <span className="font-bold text-slate-300 block">আনলকড সু্যোগ-সুবিধাসমূহ:</span>
                <div className="flex items-center gap-2 text-slate-300">
                  <Check className="w-3.5 h-3.5 text-emerald-400" />
                  <span>সরাসরি ইনস্ট্যান্ট কন্টেন্ট পাবলিশের ক্ষমতা</span>
                </div>
                <div className="flex items-center gap-2 text-slate-300">
                  <Check className="w-3.5 h-3.5 text-emerald-400" />
                  <span>প্রতি ১০০০ ভিউজে ৳২০.০০ ক্যাশ বোনাস</span>
                </div>
                <div className="flex items-center gap-2 text-slate-300">
                  <Check className="w-3.5 h-3.5 text-emerald-400" />
                  <span>কমিউনিটি ফোরামে ভেরিফাইড এক্সপার্ট ব্যাজ</span>
                </div>
              </div>

              <button
                onClick={() => setActiveTab('new-post')}
                className="w-full py-2 rounded-xl bg-gradient-to-r from-[#00f5d4] to-emerald-500 text-[#050811] font-bold text-xs cursor-pointer text-center"
              >
                নতুন পোস্ট লিখে +৫০ XP ও টাকা যোগ করুন ✍️
              </button>
            </div>
          </div>

          {/* Quick Shortcuts to other sections */}
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div
              onClick={() => {
                setActiveTab('posts');
                setStatusFilter('draft');
              }}
              className="p-5 rounded-xl bg-[#0b1120] border border-slate-800 hover:border-[#00f5d4]/40 transition-all cursor-pointer flex items-center justify-between"
            >
              <div>
                <span className="text-xs text-slate-400 font-mono">DRAFTS CENTER</span>
                <h4 className="text-lg font-bold text-white mt-0.5">{draftCount}টি ড্রাফট আর্টিকেল</h4>
                <p className="text-xs text-slate-400 mt-1">অসম্পূর্ণ আর্টিকেলগুলো এডিট করে পাবলিশ করুন</p>
              </div>
              <ChevronRight className="w-5 h-5 text-[#00f5d4]" />
            </div>

            <div
              onClick={() => setActiveTab('qa-forum')}
              className="p-5 rounded-xl bg-[#0b1120] border border-slate-800 hover:border-cyan-400/40 transition-all cursor-pointer flex items-center justify-between"
            >
              <div>
                <span className="text-xs text-cyan-400 font-mono">Q&amp;A FORUM REPLIES</span>
                <h4 className="text-lg font-bold text-white mt-0.5">{qaQuestions.length}টি প্রশ্ন ট্র্যাকিং</h4>
                <p className="text-xs text-slate-400 mt-1">কারা উত্তর দিয়েছে সরাসরি চেক করুন</p>
              </div>
              <ChevronRight className="w-5 h-5 text-cyan-400" />
            </div>

            <div
              onClick={() => setActiveTab('wallet')}
              className="p-5 rounded-xl bg-[#0b1120] border border-slate-800 hover:border-emerald-400/40 transition-all cursor-pointer flex items-center justify-between"
            >
              <div>
                <span className="text-xs text-emerald-400 font-mono">MONETARY WALLET</span>
                <h4 className="text-lg font-bold text-white mt-0.5">৳{walletBalance.toFixed(2)} ব্যালেন্স</h4>
                <p className="text-xs text-slate-400 mt-1">বিকাশ/নগদ-এ টাকা ক্যাশআউট করুন</p>
              </div>
              <ChevronRight className="w-5 h-5 text-emerald-400" />
            </div>
          </div>
        </div>
      )}

      {/* =====================================================================
          4. TAB 2: MY POSTS / CONTENT MANAGEMENT TABLE
      ====================================================================== */}
      {activeTab === 'posts' && (
        <div className="space-y-4">
          {/* Filter Bar */}
          <div className="flex flex-wrap items-center justify-between gap-3 bg-[#0b1120] border border-slate-800 rounded-xl p-3.5">
            <div className="flex flex-wrap items-center gap-2">
              <span className="text-xs text-slate-400 flex items-center gap-1 mr-1">
                <Filter className="w-3.5 h-3.5 text-[#00f5d4]" />
                ফিল্টার:
              </span>

              {(['all', 'publish', 'draft', 'pending'] as const).map((status) => (
                <button
                  key={status}
                  onClick={() => setStatusFilter(status)}
                  className={`px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer ${
                    statusFilter === status
                      ? 'bg-[#00f5d4] text-[#050811]'
                      : 'bg-[#050811] text-slate-300 hover:text-white border border-slate-800'
                  }`}
                >
                  {status === 'all'
                    ? `সকল কন্টেন্ট (${posts.length})`
                    : status === 'publish'
                    ? `পাবলিশড (${publishedCount})`
                    : status === 'draft'
                    ? `ড্রাফট (${draftCount})`
                    : `পেন্ডিং (${pendingCount})`}
                </button>
              ))}

              <span className="text-slate-600 hidden md:inline">|</span>

              <select
                value={typeFilter}
                onChange={(e) => setTypeFilter(e.target.value)}
                className="bg-[#050811] border border-slate-800 rounded-lg px-2.5 py-1 text-xs text-slate-300 focus:outline-none focus:border-[#00f5d4]"
              >
                <option value="all">সকল ধরন (All Types)</option>
                <option value="tutorial">সাধারণ আর্টিকেল ও টিউটোরিয়াল</option>
                <option value="device_review">ডিভাইস রিভিউ</option>
                <option value="tech_news">টেক নিউজ</option>
                <option value="device_price">ডিভাইসের দাম</option>
                <option value="code">হ্যাকিং কোড ও স্ক্রিপ্ট</option>
                <option value="tools">সিকিউরিটি টুলস</option>
              </select>
            </div>

            <button
              onClick={() => {
                setEditingPostId(null);
                setPostTitle('');
                setPostContent('');
                setActiveTab('new-post');
              }}
              className="px-3.5 py-1.5 rounded-lg bg-[#00f5d4] text-[#050811] font-bold text-xs flex items-center gap-1.5 cursor-pointer"
            >
              <PlusCircle className="w-3.5 h-3.5" />
              <span>নতুন আর্টিকেল লিখুন</span>
            </button>
          </div>

          {/* Posts List Cards */}
          <div className="space-y-3">
            {filteredPosts.length === 0 ? (
              <div className="p-12 text-center bg-[#0b1120] border border-slate-800 rounded-2xl space-y-3">
                <FileText className="w-10 h-10 text-slate-600 mx-auto" />
                <h4 className="text-base font-bold text-white">কোনো কন্টেন্ট খুঁজে পাওয়া যায়নি</h4>
                <p className="text-xs text-slate-400">ফিল্টার পরিবর্তন করুন অথবা একটি নতুন পোস্ট লিখুন।</p>
                <button
                  onClick={() => {
                    setEditingPostId(null);
                    setPostTitle('');
                    setPostContent('');
                    setActiveTab('new-post');
                  }}
                  className="px-4 py-2 rounded-xl bg-[#00f5d4] text-[#050811] font-bold text-xs cursor-pointer inline-flex items-center gap-1.5"
                >
                  <PlusCircle className="w-3.5 h-3.5" />
                  নতুন পোস্ট তৈরি করুন
                </button>
              </div>
            ) : (
              filteredPosts.map((post) => (
                <div
                  key={post.id}
                  className="p-5 rounded-2xl bg-[#0b1120] border border-slate-800/90 hover:border-slate-700 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4"
                >
                  <div className="space-y-2 flex-1">
                    <div className="flex flex-wrap items-center gap-2">
                      <span
                        className={`px-2 py-0.5 rounded text-[11px] font-mono font-bold uppercase ${
                          post.status === 'publish'
                            ? 'bg-emerald-500/15 border border-emerald-500/40 text-emerald-400'
                            : post.status === 'draft'
                            ? 'bg-slate-700/30 border border-slate-600 text-slate-300'
                            : 'bg-amber-500/15 border border-amber-500/40 text-amber-300'
                        }`}
                      >
                        {post.status === 'publish' ? '● Live Published' : post.status === 'draft' ? '○ Draft Saved' : '⏳ Pending Review'}
                      </span>

                      <span className="px-2 py-0.5 rounded bg-blue-500/10 border border-blue-500/30 text-blue-300 text-[11px] font-mono">
                        {post.type.replace('_', ' ')}
                      </span>

                      <span className="text-xs text-slate-400 font-mono">
                        {post.createdAt}
                      </span>
                    </div>

                    <h3
                      onClick={() => setSelectedPostPreview(post)}
                      className="text-base font-bold text-white hover:text-[#00f5d4] transition-colors cursor-pointer"
                    >
                      {post.title}
                    </h3>

                    <p className="text-xs text-slate-400 line-clamp-2 leading-relaxed">
                      {post.excerpt}
                    </p>

                    <div className="flex flex-wrap items-center gap-1.5 pt-1">
                      {post.tags.map((tag, idx) => (
                        <span key={idx} className="text-[10px] font-mono bg-[#050811] text-slate-400 px-2 py-0.5 rounded border border-slate-800">
                          #{tag}
                        </span>
                      ))}
                    </div>
                  </div>

                  {/* Metrics and Action Buttons */}
                  <div className="flex md:flex-col items-center md:items-end justify-between gap-3 pt-3 md:pt-0 border-t md:border-t-0 border-slate-800 shrink-0">
                    <div className="flex items-center gap-3 text-xs font-mono">
                      <div className="text-cyan-400 flex items-center gap-1" title="Views">
                        <Eye className="w-3.5 h-3.5" />
                        <span>{post.views.toLocaleString()}</span>
                      </div>
                      <div className="text-rose-400 flex items-center gap-1" title="Likes">
                        <Heart className="w-3.5 h-3.5" />
                        <span>{post.likes}</span>
                      </div>
                      <div className="text-purple-400 flex items-center gap-1" title="Comments">
                        <MessageSquare className="w-3.5 h-3.5" />
                        <span>{post.commentsCount}</span>
                      </div>
                    </div>

                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => setSelectedPostPreview(post)}
                        className="px-2.5 py-1.5 rounded-lg bg-[#00f5d4]/10 hover:bg-[#00f5d4]/20 border border-[#00f5d4]/30 text-[#00f5d4] text-xs font-semibold flex items-center gap-1 cursor-pointer"
                        title="পোস্ট প্রিভিউ দেখুন"
                      >
                        <Eye className="w-3 h-3" />
                        <span>প্রিভিউ</span>
                      </button>

                      <button
                        onClick={() => handleStartEdit(post)}
                        className="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1 cursor-pointer"
                        title="পোস্ট এডিট করুন"
                      >
                        <Edit3 className="w-3 h-3 text-amber-400" />
                        <span>এডিট</span>
                      </button>

                      <button
                        onClick={() => handleDeletePost(post.id, post.title)}
                        className="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 cursor-pointer"
                        title="ডিলিট করুন"
                      >
                        <Trash2 className="w-3.5 h-3.5" />
                      </button>
                    </div>
                  </div>
                </div>
              ))
            )}
          </div>
        </div>
      )}

      {/* =====================================================================
          5. TAB 3: CREATE / EDIT NEW CONTENT
      ====================================================================== */}
      {activeTab === 'new-post' && (
        <div className="rounded-2xl bg-[#0b1120] border border-[#00f5d4]/30 p-6 md:p-8 space-y-6">
          <div className="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div>
              <div className="text-xs font-mono text-[#00f5d4]">
                {editingPostId ? 'EDIT CONTENT · কন্টেন্ট সংশোধন' : 'CONTENT CREATOR STUDIO · নতুন আর্টিকেল রচনা'}
              </div>
              <h2 className="text-xl md:text-2xl font-bold text-white mt-1">
                {editingPostId ? `সংশোধন: ${postTitle || 'পোস্ট'}` : 'নতুন কন্টেন্ট লিখুন ও রিওয়ার্ড অর্জন করুন'}
              </h2>
            </div>

            <div className="flex items-center gap-2">
              <button
                onClick={() => handleSavePost('draft')}
                className="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold flex items-center gap-1.5 cursor-pointer"
              >
                💾 সেভ ড্রাফট (Draft)
              </button>
              <button
                onClick={() => handleSavePost('pending')}
                className="px-3.5 py-2 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-xs font-bold flex items-center gap-1.5 cursor-pointer"
              >
                ⏳ পেন্ডিং রিভিউ
              </button>
              <button
                onClick={() => handleSavePost('publish')}
                className="px-4 py-2 rounded-xl bg-gradient-to-r from-[#00f5d4] to-emerald-500 hover:opacity-95 text-[#050811] text-xs font-extrabold flex items-center gap-1.5 shadow-[0_0_20px_rgba(0,245,212,0.3)] cursor-pointer"
              >
                🚀 সরাসরি পাবলিশ (+৳১৫ ও +৫০ XP)
              </button>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {/* Left 2 Columns: Main Title & Body */}
            <div className="lg:col-span-2 space-y-4">
              <div>
                <label className="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                  আর্টিকেলের শিরোনাম (Title) *
                </label>
                <input
                  type="text"
                  value={postTitle}
                  onChange={(e) => setPostTitle(e.target.value)}
                  placeholder="যেমন: Python দিয়ে কীভাবে নেটওয়ার্ক প্যাকেট মনিটর করবেন..."
                  className="w-full px-4 py-2.5 rounded-xl bg-[#050811] border border-slate-800 text-white font-medium focus:outline-none focus:border-[#00f5d4] text-sm"
                />
              </div>

              {/* Dynamic Post Type Fields */}
              {postType === 'device_review' && (
                <div className="p-4 rounded-xl bg-[#050811] border border-purple-500/30 space-y-3">
                  <span className="text-xs font-mono text-purple-300 font-bold block">
                    📱 ডিভাইস রিভিউ স্পেসিফিকেশন সেটিংস (Device Review Specs)
                  </span>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label className="text-[11px] text-slate-400">ডিসপ্লে স্পেসিফিকেশন</label>
                      <input
                        type="text"
                        value={deviceDisplay}
                        onChange={(e) => setDeviceDisplay(e.target.value)}
                        className="w-full px-3 py-1.5 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white"
                      />
                    </div>
                    <div>
                      <label className="text-[11px] text-slate-400">প্রসেসর ও চিপসেট</label>
                      <input
                        type="text"
                        value={deviceProcessor}
                        onChange={(e) => setDeviceProcessor(e.target.value)}
                        className="w-full px-3 py-1.5 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white"
                      />
                    </div>
                    <div>
                      <label className="text-[11px] text-slate-400">ব্যাটারি ও চার্জিং</label>
                      <input
                        type="text"
                        value={deviceBattery}
                        onChange={(e) => setDeviceBattery(e.target.value)}
                        className="w-full px-3 py-1.5 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white"
                      />
                    </div>
                    <div>
                      <label className="text-[11px] text-slate-400">আনুমানিক দাম (BDT)</label>
                      <input
                        type="text"
                        value={devicePrice}
                        onChange={(e) => setDevicePrice(e.target.value)}
                        className="w-full px-3 py-1.5 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="text-[11px] text-slate-400">ভালো দিক (Pros - কমা দিয়ে আলাদা করুন)</label>
                    <input
                      type="text"
                      value={devicePros}
                      onChange={(e) => setDevicePros(e.target.value)}
                      className="w-full px-3 py-1.5 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white"
                    />
                  </div>
                  <div>
                    <label className="text-[11px] text-slate-400">সীমাবদ্ধতা (Cons - কমা দিয়ে আলাদা করুন)</label>
                    <input
                      type="text"
                      value={deviceCons}
                      onChange={(e) => setDeviceCons(e.target.value)}
                      className="w-full px-3 py-1.5 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white"
                    />
                  </div>
                </div>
              )}

              {postType === 'device_price' && (
                <div className="p-4 rounded-xl bg-[#050811] border border-emerald-500/30 space-y-3">
                  <span className="text-xs font-mono text-emerald-300 font-bold block">
                    💵 ডিভাইসের দাম ও ভ্যারিয়েন্ট সেটিংস (Device Pricing in Bangladesh)
                  </span>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label className="text-[11px] text-slate-400">অফিসিয়াল ও আনঅফিসিয়াল দাম (টাকা)</label>
                      <input
                        type="text"
                        value={devicePrice}
                        onChange={(e) => setDevicePrice(e.target.value)}
                        className="w-full px-3 py-1.5 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white"
                      />
                    </div>
                    <div>
                      <label className="text-[11px] text-slate-400">ভ্যারিয়েন্টস (RAM / Storage)</label>
                      <input
                        type="text"
                        defaultValue="8/128GB, 12/256GB"
                        className="w-full px-3 py-1.5 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white"
                      />
                    </div>
                  </div>
                </div>
              )}

              {postType === 'tech_news' && (
                <div className="p-4 rounded-xl bg-[#050811] border border-blue-500/30 space-y-3">
                  <span className="text-xs font-mono text-blue-300 font-bold block">
                    📰 টেক নিউজ ও তথ্যের উৎস (News Source & Verification)
                  </span>
                  <div>
                    <label className="text-[11px] text-slate-400">তথ্যের উৎস বা সোর্স নেম</label>
                    <input
                      type="text"
                      value={newsSource}
                      onChange={(e) => setNewsSource(e.target.value)}
                      className="w-full px-3 py-1.5 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white"
                    />
                  </div>
                </div>
              )}

              <div>
                <label className="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">
                  সংক্ষিপ্ত সারসংক্ষেপ (Excerpt)
                </label>
                <textarea
                  rows={2}
                  value={postExcerpt}
                  onChange={(e) => setPostExcerpt(e.target.value)}
                  placeholder="গুগল সার্চ ও সোশ্যাল শেয়ার কার্ডে এই বিবরণটি দেখা যাবে..."
                  className="w-full px-4 py-2 rounded-xl bg-[#050811] border border-slate-800 text-white text-xs focus:outline-none focus:border-[#00f5d4]"
                />
              </div>

              <div>
                <div className="flex items-center justify-between mb-1.5">
                  <label className="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                    মূল কন্টেন্ট বডি (Article Content) *
                  </label>
                  <div className="flex items-center gap-1.5 text-[11px] font-mono text-slate-400">
                    <button
                      type="button"
                      onClick={() => setPostContent((c) => c + '\n\n### নতুন সেকশন শিরোনাম\nএখানে আপনার বিষয়বস্তুর বিস্তারিত লিখুন।')}
                      className="px-2 py-0.5 rounded bg-slate-800 hover:text-white cursor-pointer"
                    >
                      +Heading
                    </button>
                    <button
                      type="button"
                      onClick={() => setPostContent((c) => c + '\n\n```python\n# এখানে আপনার কোড লিখুন\nimport scapy\nprint("Hello HackersShikkhok")\n```\n')}
                      className="px-2 py-0.5 rounded bg-slate-800 hover:text-white cursor-pointer"
                    >
                      +Code
                    </button>
                    <button
                      type="button"
                      onClick={() => setPostContent((c) => c + '\n\n> ⚠️ সতর্কতা: এই কমান্ডটি কেবল ল্যাব এবং নিজের নেটওয়ার্কে ব্যবহার করুন।\n')}
                      className="px-2 py-0.5 rounded bg-slate-800 hover:text-white cursor-pointer"
                    >
                      +Warning
                    </button>
                  </div>
                </div>
                <textarea
                  rows={10}
                  value={postContent}
                  onChange={(e) => setPostContent(e.target.value)}
                  placeholder="এখানে আপনার আর্টিকেলের পূর্ণাঙ্গ বিস্তারিত লিখুন। কোড ব্লক, উদাহরণ, কমান্ড এবং ডায়ালগ ব্যবহার করতে পারেন..."
                  className="w-full px-4 py-3 rounded-xl bg-[#050811] border border-slate-800 text-white text-xs font-sans leading-relaxed focus:outline-none focus:border-[#00f5d4]"
                />
              </div>
            </div>

            {/* Right Column: Taxonomy, Type, Tags & SEO */}
            <div className="space-y-4">
              <div className="p-4 rounded-xl bg-[#050811] border border-slate-800 space-y-3">
                <label className="block text-xs font-bold text-slate-200 uppercase tracking-wider">
                  কন্টেন্টের ধরন (Content Type)
                </label>
                <select
                  value={postType}
                  onChange={(e) => setPostType(e.target.value as UserPost['type'])}
                  className="w-full px-3 py-2 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white focus:outline-none focus:border-[#00f5d4]"
                >
                  <option value="tutorial">সাধারণ আর্টিকেল ও টিউটোরিয়াল</option>
                  <option value="device_review">মোবাইল ও গ্যাজেট রিভিউ</option>
                  <option value="tech_news">টেক নিউজ ও ব্রেকিং আপডেট</option>
                  <option value="device_price">ডিভাইসের দাম ও স্পেসিফিকেশন</option>
                  <option value="code">হ্যাকিং কোড ও স্ক্রিপ্ট</option>
                  <option value="tools">সিকিউরিটি টুলস ও ইউটিলিটি</option>
                </select>
              </div>

              <div className="p-4 rounded-xl bg-[#050811] border border-slate-800 space-y-3">
                <label className="block text-xs font-bold text-slate-200 uppercase tracking-wider">
                  ক্যাটাগরি (Category)
                </label>
                <input
                  type="text"
                  value={postCategory}
                  onChange={(e) => setPostCategory(e.target.value)}
                  className="w-full px-3 py-2 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white focus:outline-none focus:border-[#00f5d4]"
                />
              </div>

              <div className="p-4 rounded-xl bg-[#050811] border border-slate-800 space-y-3">
                <label className="block text-xs font-bold text-slate-200 uppercase tracking-wider">
                  ট্যাগসমূহ (Tags - কমা দিয়ে আলাদা করুন)
                </label>
                <input
                  type="text"
                  value={postTags}
                  onChange={(e) => setPostTags(e.target.value)}
                  placeholder="Python, Wireshark, Tutorial"
                  className="w-full px-3 py-2 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white focus:outline-none focus:border-[#00f5d4]"
                />
              </div>

              <div className="p-4 rounded-xl bg-gradient-to-br from-emerald-500/10 to-teal-500/10 border border-emerald-500/30 space-y-2">
                <span className="text-xs font-bold text-emerald-300 flex items-center gap-1.5">
                  <Award className="w-4 h-4 text-emerald-400" />
                  লেখক আর্নিং পলিসি
                </span>
                <p className="text-[11px] text-slate-300 leading-relaxed">
                  • নতুন কোয়ালিটি আর্টিকেল পাবলিশে <strong>+৳১৫.০০ BDT</strong> এবং <strong>+৫০ XP</strong> পাবেন।<br />
                  • প্রতি ১,০০০ ভেরিফাইড পাঠকে <strong>+৳২০.০০</strong> যোগ হবে।<br />
                  • ড্রাফট সেভ করলে যেকোনো সময় আবার সম্পাদনা করা যাবে।
                </p>
              </div>

              <button
                type="button"
                onClick={() => handleSavePost('publish')}
                className="w-full py-3 rounded-xl bg-gradient-to-r from-[#00f5d4] to-emerald-500 text-[#050811] font-extrabold text-sm shadow-[0_0_20px_rgba(0,245,212,0.35)] cursor-pointer"
              >
                🚀 আর্টিকেল লাইভ পাবলিশ করুন
              </button>
            </div>
          </div>
        </div>
      )}

      {/* =====================================================================
          6. TAB 4: Q&A COMMUNITY FORUM & ANSWERS
      ====================================================================== */}
      {activeTab === 'qa-forum' && (
        <div className="space-y-6">
          <div className="flex flex-wrap items-center justify-between gap-4 bg-[#0b1120] border border-slate-800 rounded-xl p-4">
            <div>
              <div className="text-xs font-mono text-cyan-400">COMMUNITY QUESTIONS &amp; EXPERT ANSWERS</div>
              <h2 className="text-xl font-bold text-white mt-0.5">
                প্রশ্নোত্তর ও ফোরাম সেন্টার (আমার প্রশ্নসমূহ ও প্রাপ্ত উত্তর)
              </h2>
            </div>
            <button
              onClick={() => setIsAskingQuestion(true)}
              className="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-[#050811] font-bold text-xs flex items-center gap-1.5 cursor-pointer shadow-[0_0_15px_rgba(6,182,212,0.3)]"
            >
              <PlusCircle className="w-3.5 h-3.5" />
              <span>নতুন প্রশ্ন করুন</span>
            </button>
          </div>

          {/* Ask Question Modal / Accordion */}
          {isAskingQuestion && (
            <div className="p-6 rounded-2xl bg-[#0b1120] border border-cyan-500/40 space-y-4">
              <div className="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 className="text-base font-bold text-white flex items-center gap-2">
                  <MessageSquare className="w-4 h-4 text-cyan-400" />
                  <span>কমিউনিটিকে নতুন প্রশ্ন জিজ্ঞাসা করুন (+১০ XP)</span>
                </h3>
                <button onClick={() => setIsAskingQuestion(false)} className="text-slate-400 hover:text-white cursor-pointer">
                  ✕
                </button>
              </div>

              <form onSubmit={handleAskQuestion} className="space-y-3">
                <div>
                  <label className="text-xs font-bold text-slate-300 block mb-1">প্রশ্নের মূল বিষয় (Title) *</label>
                  <input
                    type="text"
                    required
                    value={askTitle}
                    onChange={(e) => setAskTitle(e.target.value)}
                    placeholder="যেমন: WordPress এ Custom REST API রাউটে কীভাবে রেট লিমিট বসাবেন?"
                    className="w-full px-4 py-2 rounded-xl bg-[#050811] border border-slate-800 text-white text-xs focus:outline-none focus:border-cyan-400"
                  />
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label className="text-xs font-bold text-slate-300 block mb-1">ক্যাটাগরি</label>
                    <select
                      value={askCategory}
                      onChange={(e) => setAskCategory(e.target.value)}
                      className="w-full px-3 py-2 rounded-lg bg-[#050811] border border-slate-800 text-xs text-white"
                    >
                      <option value="Network Security">Network Security &amp; Sniffing</option>
                      <option value="WordPress Hardening">WordPress Security &amp; Plugins</option>
                      <option value="Device Security">Mobile &amp; Hardware Security</option>
                      <option value="Bug Bounty">Web App Penetration Testing</option>
                    </select>
                  </div>
                  <div>
                    <label className="text-xs font-bold text-slate-300 block mb-1">ট্যাগসমূহ</label>
                    <input
                      type="text"
                      defaultValue="WordPress, REST API, Security"
                      className="w-full px-3 py-2 rounded-lg bg-[#050811] border border-slate-800 text-xs text-white"
                    />
                  </div>
                </div>

                <div>
                  <label className="text-xs font-bold text-slate-300 block mb-1">বিস্তারিত বিবরণ ও ত্রুটির মেসেজ</label>
                  <textarea
                    rows={3}
                    value={askDetails}
                    onChange={(e) => setAskDetails(e.target.value)}
                    placeholder="কোড স্নsnippet বা আপনি ইতিমধ্যে কী চেষ্টা করেছেন তা লিখুন..."
                    className="w-full px-4 py-2 rounded-xl bg-[#050811] border border-slate-800 text-white text-xs focus:outline-none focus:border-cyan-400"
                  />
                </div>

                <div className="flex justify-end gap-2 pt-2">
                  <button
                    type="button"
                    onClick={() => setIsAskingQuestion(false)}
                    className="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-bold cursor-pointer"
                  >
                    বাতিল
                  </button>
                  <button
                    type="submit"
                    className="px-5 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-[#050811] font-bold text-xs cursor-pointer"
                  >
                    প্রশ্ন পোস্ট করুন 🚀
                  </button>
                </div>
              </form>
            </div>
          )}

          {/* Questions List */}
          <div className="space-y-4">
            {qaQuestions.map((q) => (
              <div
                key={q.id}
                className="p-5 rounded-2xl bg-[#0b1120] border border-slate-800 hover:border-cyan-500/30 transition-all space-y-4"
              >
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                  <div className="flex items-center gap-2">
                    <span className="px-2 py-0.5 rounded bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-[11px] font-mono">
                      {q.category}
                    </span>
                    <span className="text-xs text-slate-400">জিজ্ঞাসা করেছেন: {q.askedBy} ({q.askedAt})</span>
                  </div>

                  <span
                    className={`px-2.5 py-0.5 rounded text-[11px] font-mono font-bold ${
                      q.status === 'solved'
                        ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40'
                        : q.answersCount > 0
                        ? 'bg-blue-500/20 text-blue-300'
                        : 'bg-slate-800 text-slate-400'
                    }`}
                  >
                    {q.status === 'solved' ? '✓ Solved (গৃহীত উত্তর)' : `${q.answersCount}টি উত্তর পাওয়া গেছে`}
                  </span>
                </div>

                <div className="space-y-2">
                  <h3
                    onClick={() => setSelectedQuestion(selectedQuestion?.id === q.id ? null : q)}
                    className="text-base font-bold text-white hover:text-cyan-400 transition-colors cursor-pointer flex items-center justify-between gap-3"
                  >
                    <span>{q.title}</span>
                    <span className="text-xs font-mono text-cyan-400 shrink-0">
                      {selectedQuestion?.id === q.id ? 'সংকোচন করুন ▲' : 'উত্তর ও বিস্তার ▼'}
                    </span>
                  </h3>

                  {/* Highlights of who answered */}
                  {q.answers.length > 0 && (
                    <div className="p-3 rounded-xl bg-[#050811] border border-slate-800 text-xs space-y-1.5">
                      <div className="flex items-center gap-2 text-slate-300">
                        <span className="text-emerald-400 font-bold">★ উত্তর দিয়েছেন:</span>
                        {q.answers.map((a, i) => (
                          <span key={i} className="px-2 py-0.5 rounded bg-slate-800 text-slate-200 font-mono text-[11px]">
                            {a.answeredBy}
                          </span>
                        ))}
                      </div>
                      <p className="text-slate-400 line-clamp-1 italic text-[11px]">
                        "{q.answers[0].content}"
                      </p>
                    </div>
                  )}
                </div>

                {/* Direct Action Button to Jump to Question & Answers */}
                <div className="flex flex-wrap items-center justify-between gap-3 pt-2">
                  <div className="flex items-center gap-3 text-xs text-slate-400 font-mono">
                    <span>👍 {q.upvotes} আপভোট</span>
                    <span>💬 {q.answersCount} এক্সপার্ট রিপ্লাই</span>
                  </div>

                  <button
                    onClick={() => setSelectedQuestion(selectedQuestion?.id === q.id ? null : q)}
                    className="px-3.5 py-1.5 rounded-lg bg-cyan-500/15 hover:bg-cyan-500/25 border border-cyan-500/40 text-cyan-300 text-xs font-bold flex items-center gap-1.5 cursor-pointer"
                  >
                    <span>সরাসরি প্রশ্নে যান ও উত্তর দেখুন</span>
                    <ArrowUpRight className="w-3.5 h-3.5" />
                  </button>
                </div>

                {/* Deep-Linked Full Answers Drawer */}
                {selectedQuestion?.id === q.id && (
                  <div className="mt-4 pt-4 border-t border-slate-800 space-y-4">
                    <h4 className="text-xs font-bold text-[#00f5d4] uppercase tracking-wider">
                      সকল উত্তর ({q.answers.length}):
                    </h4>

                    {q.answers.map((ans) => (
                      <div
                        key={ans.id}
                        className={`p-4 rounded-xl border space-y-2 ${
                          ans.isAccepted
                            ? 'bg-emerald-950/20 border-emerald-500/50'
                            : 'bg-[#050811] border-slate-800'
                        }`}
                      >
                        <div className="flex items-center justify-between">
                          <div className="flex items-center gap-2">
                            <span className="font-mono font-bold text-white text-xs">{ans.answeredBy}</span>
                            <span className="text-[11px] text-slate-400">({ans.authorRole})</span>
                            <span className="text-[10px] text-slate-400">• {ans.answeredAt}</span>
                          </div>
                          {ans.isAccepted && (
                            <span className="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-500/40">
                              ✓ গৃহিত সেরা উত্তর
                            </span>
                          )}
                        </div>

                        <p className="text-xs text-slate-200 leading-relaxed whitespace-pre-line">
                          {ans.content}
                        </p>

                        <div className="flex items-center gap-2 pt-1">
                          <button
                            onClick={() => {
                              ans.upvotes += 1;
                              onNotify(`উত্তরটিতে আপভোট দেওয়া হয়েছে (${ans.upvotes})`);
                            }}
                            className="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-mono flex items-center gap-1 cursor-pointer"
                          >
                            ▲ আপভোট ({ans.upvotes})
                          </button>
                        </div>
                      </div>
                    ))}

                    {/* Reply Form */}
                    <form onSubmit={handleAddAnswer} className="space-y-2 pt-2">
                      <label className="text-xs font-bold text-slate-300 block">
                        আপনার উত্তর বা মতামত যোগ করুন:
                      </label>
                      <textarea
                        rows={3}
                        value={newAnswerText}
                        onChange={(e) => setNewAnswerText(e.target.value)}
                        placeholder="আপনার প্রযুক্তিগত ব্যাখ্যা বা সমাধান লিখুন..."
                        className="w-full px-4 py-2 rounded-xl bg-[#050811] border border-slate-800 text-white text-xs focus:outline-none focus:border-cyan-400"
                      />
                      <button
                        type="submit"
                        className="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-[#050811] text-xs font-bold cursor-pointer"
                      >
                        উত্তর পোস্ট করুন (+১৫ XP) 🚀
                      </button>
                    </form>
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>
      )}

      {/* =====================================================================
          7. TAB 5: WALLET & BDT TRANSACTION LEDGER
      ====================================================================== */}
      {activeTab === 'wallet' && (
        <div className="space-y-6">
          {/* Wallet Header Cards */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Monetary Wallet Card */}
            <div className="rounded-2xl bg-gradient-to-br from-[#0b1120] to-[#050811] border border-[#00f5d4]/40 p-6 space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <span className="text-xs font-mono text-[#00f5d4] uppercase font-bold">
                    REAL CASH WALLET (BDT)
                  </span>
                  <div className="text-3xl font-extrabold font-mono text-white mt-1">
                    ৳{walletBalance.toFixed(2)}
                  </div>
                  <p className="text-xs text-slate-400 mt-1">
                    কন্টেন্ট প্রকাশনা ও অডিট থেকে উপার্জিত ব্যালেন্স
                  </p>
                </div>
                <div className="w-12 h-12 rounded-2xl bg-[#00f5d4]/15 border border-[#00f5d4] flex items-center justify-center text-[#00f5d4]">
                  <Wallet className="w-6 h-6" />
                </div>
              </div>

              {/* Withdrawal Request Trigger */}
              <div className="p-4 rounded-xl bg-[#050811] border border-slate-800 space-y-3">
                <span className="text-xs font-bold text-white block">
                  টাকা ক্যাশআউট করুন (bKash / Nagad / Rocket)
                </span>
                <form onSubmit={handleWithdrawRequest} className="space-y-2.5">
                  <div className="grid grid-cols-3 gap-2">
                    {(['bKash', 'Nagad', 'Rocket'] as const).map((method) => (
                      <button
                        key={method}
                        type="button"
                        onClick={() => setWithdrawMethod(method)}
                        className={`py-1.5 rounded-lg text-xs font-bold cursor-pointer ${
                          withdrawMethod === method
                            ? 'bg-[#00f5d4] text-[#050811]'
                            : 'bg-[#0b1120] text-slate-300 border border-slate-800'
                        }`}
                      >
                        {method}
                      </button>
                    ))}
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <input
                      type="text"
                      value={withdrawAccount}
                      onChange={(e) => setWithdrawAccount(e.target.value)}
                      placeholder="অ্যাকাউন্ট নম্বর (017...)"
                      className="px-3 py-1.5 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white"
                    />
                    <input
                      type="number"
                      value={withdrawAmount}
                      onChange={(e) => setWithdrawAmount(e.target.value)}
                      placeholder="টাকার পরিমাণ (মিনিমাম ৫০)"
                      className="px-3 py-1.5 rounded-lg bg-[#0b1120] border border-slate-800 text-xs text-white"
                    />
                  </div>

                  <button
                    type="submit"
                    className="w-full py-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-[#050811] font-extrabold text-xs cursor-pointer shadow-[0_0_15px_rgba(16,185,129,0.3)]"
                  >
                    উইথড্র রিকোয়েস্ট পাঠান 💸
                  </button>
                </form>
              </div>
            </div>

            {/* Non-Monetary XP & Rank Card */}
            <div className="rounded-2xl bg-gradient-to-br from-[#0b1120] to-[#050811] border border-amber-500/40 p-6 space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <span className="text-xs font-mono text-amber-400 uppercase font-bold">
                    ENGAGEMENT &amp; REPUTATION POINTS
                  </span>
                  <div className="text-3xl font-extrabold font-mono text-amber-400 mt-1">
                    {walletPoints} XP
                  </div>
                  <p className="text-xs text-slate-400 mt-1">
                    ফোরাম উত্তর, সিটিএফ ল্যাব সমাধান ও শিক্ষামূলক অবদান
                  </p>
                </div>
                <div className="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500 flex items-center justify-center text-amber-400">
                  <Award className="w-6 h-6" />
                </div>
              </div>

              <div className="p-4 rounded-xl bg-[#050811] border border-slate-800 space-y-2 text-xs">
                <span className="font-bold text-white block">পয়েন্ট অর্জনের নিয়মাবলী:</span>
                <div className="flex justify-between text-slate-300">
                  <span>• নতুন আর্টিকেল লাইভ পাবলিশ</span>
                  <span className="text-[#00f5d4] font-mono font-bold">+50 XP</span>
                </div>
                <div className="flex justify-between text-slate-300">
                  <span>• ফোরামে সেরা উত্তর নির্বাচিত হওয়া</span>
                  <span className="text-amber-400 font-mono font-bold">+50 XP</span>
                </div>
                <div className="flex justify-between text-slate-300">
                  <span>• ফোরামে গঠনমূলক উত্তর প্রদান</span>
                  <span className="text-cyan-400 font-mono font-bold">+15 XP</span>
                </div>
                <div className="flex justify-between text-slate-300">
                  <span>• ফোরামে প্রযুক্তিগত প্রশ্ন জিজ্ঞাসা</span>
                  <span className="text-purple-400 font-mono font-bold">+10 XP</span>
                </div>
              </div>
            </div>
          </div>

          {/* Transaction History Ledger */}
          <div className="rounded-2xl bg-[#0b1120] border border-slate-800 p-6 space-y-4">
            <h3 className="text-base font-bold text-white flex items-center gap-2">
              <Clock className="w-4 h-4 text-[#00f5d4]" />
              <span>লেনদেন ও উপার্জনের বিস্তারিত হিস্ট্রি (Transaction Ledger)</span>
            </h3>

            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs">
                <thead>
                  <tr className="border-b border-slate-800 text-slate-400 font-mono">
                    <th className="pb-3">ট্রানজ্যাকশন আইডি</th>
                    <th className="pb-3">তারিখ ও সময়</th>
                    <th className="pb-3">বিবরণ / কারণ</th>
                    <th className="pb-3">পরিমাণ</th>
                    <th className="pb-3">স্ট্যাটাস</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800/60 font-mono">
                  {transactions.map((txn) => (
                    <tr key={txn.id} className="hover:bg-slate-900/50">
                      <td className="py-3 text-slate-300">{txn.id}</td>
                      <td className="py-3 text-slate-400">{txn.date}</td>
                      <td className="py-3 text-white font-sans">{txn.reason}</td>
                      <td className="py-3">
                        <span
                          className={`font-bold ${
                            txn.type === 'credit' ? 'text-emerald-400' : 'text-rose-400'
                          }`}
                        >
                          {txn.type === 'credit' ? '+' : '-'}
                          {txn.currency === 'BDT' ? `৳${txn.amount.toFixed(2)}` : `${txn.amount} XP`}
                        </span>
                      </td>
                      <td className="py-3">
                        <span className="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-[10px]">
                          ✓ {txn.status}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* =====================================================================
          8. TAB 6: NOTIFICATIONS CENTER
      ====================================================================== */}
      {activeTab === 'notifications' && (
        <div className="rounded-2xl bg-[#0b1120] border border-slate-800 p-6 space-y-4">
          <div className="flex items-center justify-between border-b border-slate-800 pb-3">
            <div>
              <h3 className="text-base font-bold text-white">নোটিফিকেশন সেন্টার</h3>
              <p className="text-xs text-slate-400">লাইক, কমেন্ট, উত্তর ও আর্নিং সংক্রান্ত সকল আপডেট</p>
            </div>
            <button
              onClick={() => {
                setUserNotifications((prev) => prev.map((n) => ({ ...n, read: true })));
                onNotify('সকল নোটিফিকেশন পঠিত হিসেবে চিহ্নিত করা হয়েছে।');
              }}
              className="text-xs text-[#00f5d4] hover:underline cursor-pointer"
            >
              সবগুলো পড়া হয়েছে হিসেবে চিহ্নিত করুন
            </button>
          </div>

          <div className="space-y-3">
            {userNotifications.map((notif) => (
              <div
                key={notif.id}
                className={`p-4 rounded-xl border transition-all flex items-start justify-between gap-3 ${
                  notif.read ? 'bg-[#050811] border-slate-800' : 'bg-[#00f5d4]/5 border-[#00f5d4]/40'
                }`}
              >
                <div className="space-y-1">
                  <div className="flex items-center gap-2">
                    <span className="text-sm font-bold text-white">{notif.title}</span>
                    <span className="text-[11px] text-slate-400 font-mono">• {notif.time}</span>
                  </div>
                  <p className="text-xs text-slate-300 leading-relaxed">{notif.detail}</p>
                </div>

                {/* Direct Action Link */}
                <div className="shrink-0 flex items-center gap-2">
                  {notif.targetPostId && (
                    <button
                      onClick={() => {
                        const target = posts.find((p) => p.id === notif.targetPostId);
                        if (target) setSelectedPostPreview(target);
                      }}
                      className="px-2.5 py-1 rounded bg-[#00f5d4]/15 text-[#00f5d4] hover:bg-[#00f5d4]/25 text-xs font-bold cursor-pointer"
                    >
                      পোস্টে যান ➔
                    </button>
                  )}
                  {notif.targetQuestionId && (
                    <button
                      onClick={() => {
                        const target = qaQuestions.find((q) => q.id === notif.targetQuestionId);
                        if (target) {
                          setActiveTab('qa-forum');
                          setSelectedQuestion(target);
                        }
                      }}
                      className="px-2.5 py-1 rounded bg-cyan-500/15 text-cyan-300 hover:bg-cyan-500/25 text-xs font-bold cursor-pointer"
                    >
                      উত্তর দেখুন ➔
                    </button>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* =====================================================================
          9. MODAL: POST PREVIEW MODAL
      ====================================================================== */}
      {selectedPostPreview && (
        <div className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
          <div className="relative w-full max-w-3xl bg-[#0b1120] border-2 border-[#00f5d4] rounded-2xl p-6 md:p-8 space-y-6 max-h-[90vh] overflow-y-auto shadow-2xl">
            <button
              onClick={() => setSelectedPostPreview(null)}
              className="absolute top-5 right-5 text-slate-400 hover:text-white cursor-pointer"
            >
              <X className="w-6 h-6" />
            </button>

            <div className="space-y-2">
              <div className="flex flex-wrap items-center gap-2">
                <span className="px-2.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-xs font-mono font-bold uppercase">
                  {selectedPostPreview.type.replace('_', ' ')}
                </span>
                <span className="text-xs text-slate-400">• {selectedPostPreview.category}</span>
                <span className="text-xs text-slate-400">• প্রকাশিত: {selectedPostPreview.createdAt}</span>
              </div>
              <h2 className="text-xl sm:text-2xl font-extrabold text-white leading-snug">
                {selectedPostPreview.title}
              </h2>
            </div>

            {/* If has Specs */}
            {selectedPostPreview.specs && (
              <div className="p-4 rounded-xl bg-[#050811] border border-slate-800 space-y-3">
                <span className="text-xs font-mono text-[#00f5d4] font-bold block uppercase">
                  স্পেসিফিকেশন ও বাংলাদেশ প্রাইস
                </span>
                <div className="grid grid-cols-2 gap-2 text-xs">
                  {selectedPostPreview.specs.display && (
                    <div><span className="text-slate-400">ডিসপ্লে:</span> <strong className="text-white">{selectedPostPreview.specs.display}</strong></div>
                  )}
                  {selectedPostPreview.specs.processor && (
                    <div><span className="text-slate-400">প্রসেসর:</span> <strong className="text-white">{selectedPostPreview.specs.processor}</strong></div>
                  )}
                  {selectedPostPreview.specs.battery && (
                    <div><span className="text-slate-400">ব্যাটারি:</span> <strong className="text-white">{selectedPostPreview.specs.battery}</strong></div>
                  )}
                  {selectedPostPreview.specs.priceBdt && (
                    <div><span className="text-slate-400">দাম:</span> <strong className="text-emerald-400">{selectedPostPreview.specs.priceBdt}</strong></div>
                  )}
                </div>
              </div>
            )}

            {/* Body */}
            <div className="text-xs sm:text-sm text-slate-200 leading-relaxed whitespace-pre-line border-t border-slate-800 pt-4">
              {selectedPostPreview.content}
            </div>

            {/* Footer metrics inside modal */}
            <div className="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-800">
              <div className="flex items-center gap-3 text-xs font-mono">
                <span className="text-cyan-400">👁️ {selectedPostPreview.views.toLocaleString()} ভিউজ</span>
                <span className="text-rose-400">❤️ {selectedPostPreview.likes} লাইক</span>
                <span className="text-purple-400">💬 {selectedPostPreview.commentsCount} মন্তব্য</span>
              </div>

              <div className="flex items-center gap-2">
                <button
                  onClick={() => {
                    selectedPostPreview.likes += 1;
                    onNotify('পোস্টে লাইক যোগ করা হয়েছে!');
                  }}
                  className="px-3 py-1.5 rounded-lg bg-rose-500/20 text-rose-300 border border-rose-500/40 text-xs font-bold cursor-pointer"
                >
                  ❤️ লাইক দিন
                </button>
                <button
                  onClick={() => setSelectedPostPreview(null)}
                  className="px-4 py-1.5 rounded-lg bg-slate-800 text-slate-200 text-xs font-bold cursor-pointer"
                >
                  বন্ধ করুন
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
