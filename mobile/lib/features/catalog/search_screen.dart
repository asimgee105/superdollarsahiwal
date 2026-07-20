import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/storage/hive_storage.dart';
import '../../core/theme/app_theme.dart';

class SearchScreen extends StatefulWidget {
  const SearchScreen({super.key});

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  final _searchController = TextEditingController();
  List<String> _history = [];
  final List<String> _popularSuggestions = [
    'Kurta Set',
    'Denim Jeans',
    'Sneakers',
    'Kurtis',
    'Jackets',
    'Summer T-Shirts',
    'Handbags'
  ];

  @override
  void initState() {
    super.initState();
    _loadHistory();
  }

  void _loadHistory() {
    final list = HiveStorage.searchHistory.get('queries', defaultValue: <dynamic>[]) as List;
    setState(() {
      _history = list.map((e) => e.toString()).toList();
    });
  }

  void _saveQuery(String q) {
    if (q.isEmpty) return;
    final updated = List<String>.from(_history);
    updated.remove(q);
    updated.insert(0, q);
    if (updated.length > 8) {
      updated.removeLast();
    }
    HiveStorage.searchHistory.put('queries', updated);
    _loadHistory();
  }

  void _clearHistory() {
    HiveStorage.searchHistory.delete('queries');
    setState(() {
      _history = [];
    });
  }

  void _triggerVoiceSearch() {
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const SizedBox(height: 10),
              const Icon(Icons.mic, size: 64, color: AppTheme.primaryColor),
              const SizedBox(height: 20),
              const Text(
                'LISTENING...',
                style: TextStyle(fontWeight: FontWeight.w900, fontSize: 13, color: Colors.grey, letterSpacing: 1.0),
              ),
              const SizedBox(height: 8),
              const Text(
                'Try saying "Kurta sets under 5000"',
                style: TextStyle(fontSize: 10, color: Colors.black54),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 20),
              TextButton(
                onPressed: () {
                  Navigator.pop(context);
                  _searchController.text = 'Premium Kurta Set';
                  _submitSearch('Premium Kurta Set');
                },
                child: const Text('Simulate speech matched: "Premium Kurta Set"', style: TextStyle(fontSize: 10, color: AppTheme.primaryColor, fontWeight: FontWeight.bold)),
              )
            ],
          ),
        );
      },
    );
  }

  void _submitSearch(String query) {
    final q = query.trim();
    if (q.isEmpty) return;
    _saveQuery(q);
    context.go('/catalog?search=$q');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        titleSpacing: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/home'),
        ),
        title: Padding(
          padding: const EdgeInsets.only(right: 16.0),
          child: TextField(
            controller: _searchController,
            autofocus: true,
            onSubmitted: _submitSearch,
            decoration: InputDecoration(
              hintText: 'Search clothes, brands, or trends...',
              hintStyle: const TextStyle(fontSize: 12),
              contentPadding: const EdgeInsets.symmetric(vertical: 8),
              prefixIcon: const Icon(Icons.search, size: 20, color: Colors.grey),
              suffixIcon: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  if (_searchController.text.isNotEmpty)
                    IconButton(
                      icon: const Icon(Icons.clear, size: 18),
                      onPressed: () {
                        _searchController.clear();
                        setState(() {});
                      },
                    ),
                  IconButton(
                    icon: const Icon(Icons.mic_none, size: 20, color: AppTheme.primaryColor),
                    onPressed: _triggerVoiceSearch,
                  ),
                ],
              ),
              filled: true,
              fillColor: const Color(0xFFFAFAFA),
              border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
            ),
            style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
            onChanged: (_) => setState(() {}),
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Search History
            if (_history.isNotEmpty) ...[
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('RECENT SEARCHES', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 0.5)),
                  GestureDetector(
                    onTap: _clearHistory,
                    child: const Text('CLEAR ALL', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: AppTheme.primaryColor)),
                  )
                ],
              ),
              const SizedBox(height: 12),
              Wrap(
                spacing: 8,
                runSpacing: 8,
                children: _history.map((q) {
                  return GestureDetector(
                    onTap: () {
                      _searchController.text = q;
                      _submitSearch(q);
                    },
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFAFAFA),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: Colors.grey.shade200),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const Icon(Icons.history, size: 12, color: Colors.grey),
                          const SizedBox(width: 6),
                          Text(q, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.black87)),
                        ],
                      ),
                    ),
                  );
                }).toList(),
              ),
              const SizedBox(height: 32),
            ],

            // Popular Suggestions
            const Text('POPULAR SEARCHES', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 0.5)),
            const SizedBox(height: 16),
            ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: _popularSuggestions.length,
              itemBuilder: (context, index) {
                final suggestion = _popularSuggestions[index];
                return ListTile(
                  contentPadding: EdgeInsets.zero,
                  leading: const Icon(Icons.trending_up, size: 16, color: Colors.grey),
                  title: Text(suggestion, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.black87)),
                  trailing: const Icon(Icons.north_west, size: 14, color: Colors.grey),
                  onTap: () {
                    _searchController.text = suggestion;
                    _submitSearch(suggestion);
                  },
                );
              },
            ),
          ],
        ),
      ),
    );
  }
}
