import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/network/api_client.dart';
import '../../core/theme/app_theme.dart';

class FaqPoliciesScreen extends StatefulWidget {
  const FaqPoliciesScreen({super.key});

  @override
  State<FaqPoliciesScreen> createState() => _FaqPoliciesScreenState();
}

class _FaqPoliciesScreenState extends State<FaqPoliciesScreen> {
  final ApiClient _apiClient = ApiClient();
  List<dynamic> _faqs = [];
  bool _isLoading = true;
  String _activeTab = 'faqs'; // faqs, privacy, shipping, returns, terms

  String _policyTitle = 'Privacy Policy';
  String _policyContent = 'Loading policy content...';

  @override
  void initState() {
    super.initState();
    _fetchFaqs();
  }

  Future<void> _fetchFaqs() async {
    setState(() => _isLoading = true);
    try {
      final res = await _apiClient.get('/api/v1/faqs');
      if (res.statusCode == 200) {
        setState(() {
          _faqs = res.data ?? [];
          _isLoading = false;
        });
      } else {
        _loadFallbackFaqs();
      }
    } catch (e) {
      _loadFallbackFaqs();
    }
  }

  void _loadFallbackFaqs() {
    setState(() {
      _faqs = [
        {
          'question': 'What is the return timeframe policy?',
          'answer': 'You can file returns within 7 days of package delivery. The products must be unworn and contain tags.'
        },
        {
          'question': 'How long does shipment take?',
          'answer': 'Standard shipping takes 3-5 business days. Express shipping delivery completes in 24-48 hours.'
        },
        {
          'question': 'Can I exchange size variants?',
          'answer': 'Yes! You can file exchange requests directly on the order status timeline panel in your account profile.'
        }
      ];
      _isLoading = false;
    });
  }

  Future<void> _fetchPolicyPage(String slug) async {
    setState(() => _isLoading = true);
    try {
      final res = await _apiClient.get('/api/v1/page/$slug');
      if (res.statusCode == 200 && res.data != null) {
        setState(() {
          _policyTitle = res.data['title'] ?? 'Policy';
          _policyContent = res.data['content'] ?? 'No policy content available.';
          _isLoading = false;
        });
      } else {
        _loadFallbackPolicy(slug);
      }
    } catch (e) {
      _loadFallbackPolicy(slug);
    }
  }

  void _loadFallbackPolicy(String slug) {
    String title = 'Privacy Policy';
    String content = 'This privacy policy details how Super Dollar collects, uses, and secures user account data.';
    if (slug == 'shipping-policy') {
      title = 'Shipping Policy';
      content = 'We deliver packages nationwide. Standard shipping is Rs. 150. Orders above Rs. 5000 qualify for free express delivery.';
    } else if (slug == 'refund-policy') {
      title = 'Refund Policy';
      content = 'Once your returned items pass warehouse QC inspection checks, refund credits are automatically dispatched to original payment wallets.';
    } else if (slug == 'terms-conditions') {
      title = 'Terms & Conditions';
      content = 'By using the Super Dollar enterprise shopping portal, you agree to comply with our commercial terms and conditions.';
    }

    setState(() {
      _policyTitle = title;
      _policyContent = content;
      _isLoading = false;
    });
  }

  void _handleTabChange(String tab) {
    setState(() {
      _activeTab = tab;
    });
    if (tab == 'faqs') {
      _fetchFaqs();
    } else if (tab == 'privacy') {
      _fetchPolicyPage('privacy-policy');
    } else if (tab == 'shipping') {
      _fetchPolicyPage('shipping-policy');
    } else if (tab == 'returns') {
      _fetchPolicyPage('refund-policy');
    } else if (tab == 'terms') {
      _fetchPolicyPage('terms-conditions');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('HELP & SUPPORT POLICIES'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/profile'),
        ),
      ),
      body: Column(
        children: [
          // Horizontal Tab Bar
          SizedBox(
            height: 48,
            child: ListView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 12),
              children: [
                _buildTab('FAQs', 'faqs'),
                _buildTab('Privacy', 'privacy'),
                _buildTab('Shipping', 'shipping'),
                _buildTab('Returns', 'returns'),
                _buildTab('Terms', 'terms'),
              ],
            ),
          ),
          const SizedBox(height: 12),

          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(AppTheme.primaryColor)))
                : Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: _activeTab == 'faqs' ? _buildFaqList() : _buildPolicyContent(),
                  ),
          )
        ],
      ),
    );
  }

  Widget _buildTab(String label, String tabKey) {
    final isSelected = _activeTab == tabKey;
    return GestureDetector(
      onTap: () => _handleTabChange(tabKey),
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 4, vertical: 6),
        padding: const EdgeInsets.symmetric(horizontal: 16),
        decoration: BoxDecoration(
          color: isSelected ? AppTheme.primaryColor : Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: isSelected ? AppTheme.primaryColor : Colors.grey.shade200),
        ),
        child: Center(
          child: Text(
            label,
            style: TextStyle(
              color: isSelected ? Colors.white : Colors.black87,
              fontSize: 10,
              fontWeight: FontWeight.w900,
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildFaqList() {
    if (_faqs.isEmpty) {
      return const Center(child: Text('No FAQs available.'));
    }
    return ListView.builder(
      itemCount: _faqs.length,
      itemBuilder: (context, index) {
        final faq = _faqs[index];
        return Card(
          margin: const EdgeInsets.only(bottom: 12),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          color: Colors.white,
          elevation: 0,
          child: ExpansionTile(
            title: Text(
              faq['question'] ?? '',
              style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.black87),
            ),
            children: [
              Padding(
                padding: const EdgeInsets.only(left: 16.0, right: 16.0, bottom: 16.0),
                child: Text(
                  faq['answer'] ?? '',
                  style: const TextStyle(fontSize: 10, height: 1.5, color: Colors.black54),
                ),
              )
            ],
          ),
        );
      },
    );
  }

  Widget _buildPolicyContent() {
    return SingleChildScrollView(
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: Colors.grey.shade200),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(
              _policyTitle.toUpperCase(),
              style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w900, color: AppTheme.primaryColor, letterSpacing: 0.5),
            ),
            const SizedBox(height: 16),
            Text(
              _policyContent,
              style: const TextStyle(fontSize: 11, height: 1.6, color: Colors.black87),
            ),
          ],
        ),
      ),
    );
  }
}
