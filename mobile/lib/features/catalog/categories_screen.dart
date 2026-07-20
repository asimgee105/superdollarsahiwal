import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/network/api_client.dart';
import '../../core/theme/app_theme.dart';

class CategoriesScreen extends StatefulWidget {
  const CategoriesScreen({super.key});

  @override
  State<CategoriesScreen> createState() => _CategoriesScreenState();
}

class _CategoriesScreenState extends State<CategoriesScreen> {
  final ApiClient _apiClient = ApiClient();
  List<dynamic> _categoriesTree = [];
  bool _isLoading = true;
  int _activeParentIndex = 0;

  @override
  void initState() {
    super.initState();
    _fetchCategories();
  }

  Future<void> _fetchCategories() async {
    try {
      final res = await _apiClient.get('/api/v1/categories');
      if (res.statusCode == 200) {
        setState(() {
          _categoriesTree = res.data ?? [];
          _isLoading = false;
        });
      } else {
        _loadFallbackCategories();
      }
    } catch (e) {
      _loadFallbackCategories();
    }
  }

  void _loadFallbackCategories() {
    setState(() {
      _categoriesTree = [
        {
          'name': 'Men',
          'slug': 'men',
          'children': [
            {'name': 'Topwear', 'slug': 'men-topwear'},
            {'name': 'Bottomwear', 'slug': 'men-bottomwear'},
            {'name': 'Footwear', 'slug': 'men-footwear'},
            {'name': 'Accessories', 'slug': 'men-accessories'},
          ]
        },
        {
          'name': 'Women',
          'slug': 'women',
          'children': [
            {'name': 'Indian & Fusion Wear', 'slug': 'women-indian-wear'},
            {'name': 'Western Wear', 'slug': 'women-western-wear'},
            {'name': 'Footwear', 'slug': 'women-footwear'},
            {'name': 'Beauty & Makeup', 'slug': 'women-beauty'},
          ]
        },
        {
          'name': 'Kids',
          'slug': 'kids',
          'children': [
            {'name': 'Boys Clothing', 'slug': 'boys-clothing'},
            {'name': 'Girls Clothing', 'slug': 'girls-clothing'},
            {'name': 'Infants', 'slug': 'infants-wear'},
          ]
        },
        {
          'name': 'Beauty',
          'slug': 'beauty',
          'children': [
            {'name': 'Makeup', 'slug': 'makeup'},
            {'name': 'Skincare', 'slug': 'skincare'},
            {'name': 'Fragrances', 'slug': 'fragrances'},
          ]
        }
      ];
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('CATEGORIES'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/home'),
        ),
      ),
      body: _isLoading
          ? const Center(
              child: CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(AppTheme.primaryColor),
              ),
            )
          : _categoriesTree.isEmpty
              ? const Center(child: Text('No categories available.'))
              : Row(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Left Column: Parent categories list
                    Container(
                      width: 100,
                      color: const Color(0xFFFAFAFA),
                      child: ListView.builder(
                        itemCount: _categoriesTree.length,
                        itemBuilder: (context, index) {
                          final cat = _categoriesTree[index];
                          final name = cat['name'] ?? '';
                          final isActive = index == _activeParentIndex;

                          return GestureDetector(
                            onTap: () => setState(() => _activeParentIndex = index),
                            child: Container(
                              padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 12),
                              decoration: BoxDecoration(
                                color: isActive ? Colors.white : Colors.transparent,
                                border: Border(
                                  left: BorderSide(
                                    color: isActive ? AppTheme.primaryColor : Colors.transparent,
                                    width: 3.5,
                                  ),
                                ),
                              ),
                              child: Text(
                                name.toUpperCase(),
                                style: TextStyle(
                                  fontSize: 10,
                                  fontWeight: isActive ? FontWeight.w900 : FontWeight.bold,
                                  color: isActive ? AppTheme.primaryColor : Colors.black54,
                                ),
                              ),
                            ),
                          );
                        },
                      ),
                    ),

                    // Right Column: Child categories grid/list
                    Expanded(
                      child: Container(
                        padding: const EdgeInsets.all(16),
                        color: Colors.white,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Promo Banner
                            Container(
                              height: 100,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(12),
                                gradient: const LinearGradient(
                                  colors: [Color(0xFF2C3E50), Color(0xFF3498DB)],
                                  begin: Alignment.topLeft,
                                  end: Alignment.bottomRight,
                                ),
                              ),
                              child: Stack(
                                children: [
                                  Positioned(
                                    right: -10,
                                    bottom: -10,
                                    child: Opacity(
                                      opacity: 0.15,
                                      child: Icon(Icons.style, size: 100, color: Colors.white),
                                    ),
                                  ),
                                  const Padding(
                                    padding: EdgeInsets.all(16.0),
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      mainAxisAlignment: MainAxisAlignment.center,
                                      children: [
                                        Text(
                                          'NEW ARRIVALS',
                                          style: TextStyle(color: Colors.white, fontSize: 8, fontWeight: FontWeight.bold),
                                        ),
                                        SizedBox(height: 4),
                                        Text(
                                          'UP TO 40% OFF',
                                          style: TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w900, letterSpacing: 0.5),
                                        ),
                                      ],
                                    ),
                                  )
                                ],
                              ),
                            ),
                            const SizedBox(height: 20),

                            Expanded(
                              child: ListView.builder(
                                itemCount: (_categoriesTree[_activeParentIndex]['children'] as List?)?.length ?? 0,
                                itemBuilder: (context, idx) {
                                  final child = _categoriesTree[_activeParentIndex]['children'][idx];
                                  final name = child['name'] ?? '';
                                  final slug = child['slug'] ?? '';

                                  return Container(
                                    margin: const EdgeInsets.only(bottom: 12),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFFAFAFA),
                                      borderRadius: BorderRadius.circular(8),
                                    ),
                                    child: ListTile(
                                      title: Text(
                                        name,
                                        style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.black87),
                                      ),
                                      trailing: const Icon(Icons.arrow_forward_ios, size: 12, color: Colors.grey),
                                      onTap: () {
                                        context.go('/catalog?category=$slug');
                                      },
                                    ),
                                  );
                                },
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
    );
  }
}
