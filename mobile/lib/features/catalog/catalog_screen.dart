import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/network/api_client.dart';

class CatalogScreen extends StatefulWidget {
  final String? initialCategory;
  const CatalogScreen({super.key, this.initialCategory});

  @override
  State<CatalogScreen> createState() => _CatalogScreenState();
}

class _CatalogScreenState extends State<CatalogScreen> {
  final ApiClient _apiClient = ApiClient();
  final _searchController = TextEditingController();
  List<dynamic> _allProducts = [];
  List<dynamic> _filteredProducts = [];
  bool _isLoading = true;
  String _activeFilter = 'All';

  @override
  void initState() {
    super.initState();
    if (widget.initialCategory != null) {
      _activeFilter = widget.initialCategory!;
    }
    _fetchProducts();
  }

  Future<void> _fetchProducts() async {
    try {
      final res = await _apiClient.get('/api/v1/products');
      List<dynamic> products = [];
      if (res.statusCode == 200 && res.data['data'] != null) {
        products = res.data['data'];
      }

      setState(() {
        if (products.isNotEmpty) {
          _allProducts = products;
        } else {
          _allProducts = _getFallbackProducts();
        }
        _filterProducts();
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _allProducts = _getFallbackProducts();
        _filterProducts();
        _isLoading = false;
      });
    }
  }

  List<Map<String, String>> _getFallbackProducts() {
    return [
      {
        'id': '1',
        'title': 'Premium Cotton Kurta Set',
        'price': '4,500',
        'brand': 'SD PREMIUM',
        'image': 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=300&auto=format&fit=crop'
      },
      {
        'id': '2',
        'title': 'Slim Fit Casual Denim Jeans',
        'price': '2,200',
        'brand': 'SD MEN',
        'image': 'https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=300&auto=format&fit=crop'
      },
      {
        'id': '3',
        'title': 'Women Printed Ethnic A-Line Kurti',
        'price': '1,850',
        'brand': 'SD FUSION',
        'image': 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?q=80&w=300&auto=format&fit=crop'
      },
      {
        'id': '4',
        'title': 'Sporty Running Shoes',
        'price': '3,999',
        'brand': 'SD ACTIVE',
        'image': 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=300&auto=format&fit=crop'
      }
    ];
  }

  void _filterProducts() {
    final query = _searchController.text.trim().toLowerCase();
    setState(() {
      _filteredProducts = _allProducts.where((prod) {
        final title = (prod['title'] ?? prod['name'] ?? '').toString().toLowerCase();
        final brand = (prod['brand'] ?? '').toString().toLowerCase();
        final matchesSearch = title.contains(query) || brand.contains(query);

        if (_activeFilter == 'All') {
          return matchesSearch;
        } else {
          final cat = (prod['category'] ?? '').toString().toLowerCase();
          return matchesSearch && cat.contains(_activeFilter.toLowerCase());
        }
      }).toList();
    });
  }

  @override
  Widget build(BuildContext context) {
    final themeColor = const Color(0xFFFF3F6C);

    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('EXPLORE STYLE'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/home'),
        ),
      ),
      body: Column(
        children: [
          // Search Input
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: TextField(
              controller: _searchController,
              onChanged: (_) => _filterProducts(),
              decoration: InputDecoration(
                hintText: 'Search for clothes, shoes, or accessory...',
                prefixIcon: const Icon(Icons.search, color: Colors.grey),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear, color: Colors.grey),
                        onPressed: () {
                          _searchController.clear();
                          _filterProducts();
                        },
                      )
                    : null,
                filled: true,
                fillColor: Colors.white,
                contentPadding: const EdgeInsets.symmetric(vertical: 14),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: BorderSide(color: Colors.grey.shade200),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: BorderSide(color: Colors.grey.shade100),
                ),
              ),
            ),
          ),

          // Total counts indicator
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 4.0),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  '${_filteredProducts.length} Items Found',
                  style: const TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.w900,
                    color: Colors.grey,
                  ),
                ),
                Icon(Icons.tune, color: themeColor, size: 18),
              ],
            ),
          ),
          const SizedBox(height: 12),

          // Catalog Grid View
          Expanded(
            child: _isLoading
                ? Center(child: CircularProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(themeColor)))
                : _filteredProducts.isEmpty
                    ? Center(
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(Icons.search_off, size: 48, color: Colors.grey.shade300),
                            const SizedBox(height: 16),
                            const Text('No products match your search.', style: TextStyle(color: Colors.grey, fontSize: 12)),
                          ],
                        ),
                      )
                    : GridView.builder(
                        padding: const EdgeInsets.all(16),
                        physics: const BouncingScrollPhysics(),
                        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 2,
                          crossAxisSpacing: 16,
                          mainAxisSpacing: 20,
                          childAspectRatio: 0.72,
                        ),
                        itemCount: _filteredProducts.length,
                        itemBuilder: (context, index) {
                          final prod = _filteredProducts[index];
                          final id = prod['id'].toString();
                          final price = prod['price'] != null ? prod['price'].toString() : '0';
                          final title = prod['title'] ?? prod['name'] ?? 'Product';
                          final brand = prod['brand'] ?? 'SD COLLECTION';
                          final image = prod['image'] ?? 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=300&auto=format&fit=crop';

                          return GestureDetector(
                            onTap: () => context.go('/catalog/detail/$id'),
                            child: Container(
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(color: Colors.grey.shade100),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.black.withOpacity(0.01),
                                    blurRadius: 10,
                                    offset: const Offset(0, 4),
                                  )
                                ],
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.stretch,
                                children: [
                                  Expanded(
                                    child: Stack(
                                      children: [
                                        Container(
                                          decoration: BoxDecoration(
                                            borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
                                            image: DecorationImage(
                                              image: NetworkImage(image),
                                              fit: BoxFit.cover,
                                            ),
                                          ),
                                        ),
                                        Positioned(
                                          top: 8,
                                          right: 8,
                                          child: Container(
                                            padding: const EdgeInsets.all(6),
                                            decoration: const BoxDecoration(
                                              color: Colors.white,
                                              shape: BoxShape.circle,
                                            ),
                                            child: Icon(Icons.favorite_border, size: 14, color: themeColor),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  Padding(
                                    padding: const EdgeInsets.all(12.0),
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          brand.toUpperCase(),
                                          style: TextStyle(
                                            fontSize: 8,
                                            fontWeight: FontWeight.w900,
                                            color: themeColor,
                                            letterSpacing: 1.0,
                                          ),
                                        ),
                                        const SizedBox(height: 4),
                                        Text(
                                          title,
                                          style: const TextStyle(
                                            fontSize: 11,
                                            fontWeight: FontWeight.bold,
                                            color: Colors.black87,
                                          ),
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                        const SizedBox(height: 8),
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                          children: [
                                            Text(
                                              'Rs. $price',
                                              style: const TextStyle(
                                                fontSize: 12,
                                                fontWeight: FontWeight.w900,
                                                color: Colors.black,
                                              ),
                                            ),
                                            Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                              decoration: BoxDecoration(
                                                color: Colors.green.shade50,
                                                borderRadius: BorderRadius.circular(4),
                                              ),
                                              child: Text(
                                                'ACTIVE',
                                                style: TextStyle(color: Colors.green.shade800, fontSize: 8, fontWeight: FontWeight.bold),
                                              ),
                                            )
                                          ],
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
          ),
        ],
      ),
    );
  }
}
