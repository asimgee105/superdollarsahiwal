import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:go_router/go_router.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../../core/storage/hive_storage.dart';
import '../../core/network/api_client.dart';

class ProductDetailScreen extends StatefulWidget {
  final int productId;
  const ProductDetailScreen({super.key, required this.productId});

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  final ApiClient _apiClient = ApiClient();
  final List<String> _sizes = ['S', 'M', 'L', 'XL'];
  final List<Color> _colors = [Colors.red, Colors.blue, Colors.black, Colors.indigo];

  String _selectedSize = 'M';
  Color _selectedColor = Colors.black;
  bool _isLoading = true;

  // Mock details matching static products
  final Map<String, dynamic> _mockDetails = {
    'title': 'Premium Cotton Kurta Set',
    'brand': 'SD PREMIUM',
    'price': 4500,
    'image': 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=400&auto=format&fit=crop',
    'description': 'This premium ethnic wear set features a high-density cotton weave design with detailed embroidery along the collar, paired with straight trousers for optimal everyday wear.',
  };

  @override
  void initState() {
    super.initState();
    _fetchDetails();
  }

  Future<void> _fetchDetails() async {
    try {
      final res = await _apiClient.get('/api/v1/products/${widget.productId}');
      if (res.statusCode == 200 && res.data['data'] != null) {
        final data = res.data['data'];
        setState(() {
          _mockDetails['title'] = data['title'] ?? data['name'] ?? _mockDetails['title'];
          _mockDetails['brand'] = data['brand'] ?? _mockDetails['brand'];
          _mockDetails['price'] = data['price'] != null ? (data['price'] as num).toInt() : _mockDetails['price'];
          _mockDetails['image'] = data['image'] ?? _mockDetails['image'];
          _mockDetails['description'] = data['description'] ?? _mockDetails['description'];
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
      }
    } catch (e) {
      setState(() => _isLoading = false);
    }
  }

  void _handleAddToBag() async {
    const storage = FlutterSecureStorage();
    final token = await storage.read(key: 'auth_token');

    if (token == null) {
      if (mounted) {
        context.go('/login');
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Please login to add items to your shopping bag.')),
        );
      }
      return;
    }

    final cartBox = HiveStorage.cart;
    final id = widget.productId.toString();
    
    // Simple mock cart structure saving
    final existing = cartBox.get(id);
    if (existing != null) {
      existing['quantity'] = (existing['quantity'] ?? 1) + 1;
      cartBox.put(id, existing);
    } else {
      cartBox.put(id, {
        'id': id,
        'title': _mockDetails['title'],
        'brand': _mockDetails['brand'],
        'price': _mockDetails['price'],
        'image': _mockDetails['image'],
        'quantity': 1,
      });
    }

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('${_mockDetails['title']} added to your shopping bag!'),
        action: SnackBarAction(
          label: 'GO TO CART',
          textColor: Colors.white,
          onPressed: () => context.go('/cart'),
        ),
      ),
    );
  }

  void _handleToggleWishlist() {
    final wishlist = HiveStorage.wishlist;
    final id = widget.productId.toString();
    final inWishlist = wishlist.get(id, defaultValue: false);
    
    wishlist.put(id, !inWishlist);
    setState(() {});
    
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(!inWishlist ? 'Added to wishlist!' : 'Removed from wishlist!'),
        duration: const Duration(seconds: 1),
      ),
    );
  }

  void _showAIAssistant() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (context) {
        return Padding(
          padding: EdgeInsets.only(
            bottom: MediaQuery.of(context).viewInsets.bottom,
            top: 24,
            left: 20,
            right: 20,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Row(
                children: [
                  Icon(Icons.auto_awesome, color: Color(0xFFFF3F6C)),
                  SizedBox(width: 8),
                  Text(
                    'AI PRODUCT INSIGHTS',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w900,
                      letterSpacing: 1.2,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              const Text(
                '• Sizing Advice: Fits true to size. If you prefer a loose look, order one size up.\n'
                '• Care Guidelines: Cold machine wash. Warm iron inside out.\n'
                '• Style Tips: Style this Kurta with white straight pants and leather sandals.',
                style: TextStyle(fontSize: 11, height: 1.6, color: Colors.black87),
              ),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: () => Navigator.pop(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.black,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                ),
                child: const Text('DISMISS', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
              ),
              const SizedBox(height: 24),
            ],
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final id = widget.productId.toString();
    final inWishlist = HiveStorage.wishlist.get(id, defaultValue: false);
    final themeColor = const Color(0xFFFF3F6C);

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.black),
          onPressed: () => context.go('/catalog'),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.share_outlined, color: Colors.black),
            onPressed: () {},
          ),
        ],
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(themeColor)))
          : SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // Product Banner Image
                  Container(
                    height: 400,
                    decoration: BoxDecoration(
                      image: DecorationImage(
                        image: NetworkImage(_mockDetails['image']),
                        fit: BoxFit.cover,
                      ),
                    ),
                  ),
                  const SizedBox(height: 18),

                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Brand & AI insights row
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              _mockDetails['brand']!.toUpperCase(),
                              style: TextStyle(
                                fontSize: 10,
                                fontWeight: FontWeight.w900,
                                color: themeColor,
                                letterSpacing: 1.5,
                              ),
                            ),
                            TextButton.icon(
                              onPressed: _showAIAssistant,
                              icon: Icon(Icons.auto_awesome, size: 14, color: themeColor),
                              label: Text(
                                'AI INSIGHTS',
                                style: TextStyle(color: themeColor, fontSize: 9, fontWeight: FontWeight.bold),
                              ),
                              style: TextButton.styleFrom(
                                padding: EdgeInsets.zero,
                                minimumSize: Size.zero,
                                tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                              ),
                            )
                          ],
                        ),
                        const SizedBox(height: 6),
                        Text(
                          _mockDetails['title']!,
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: Colors.black87,
                          ),
                        ),
                        const SizedBox(height: 12),
                        Text(
                          'Rs. ${_mockDetails['price'].toString()}',
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.w900,
                            color: Colors.black,
                          ),
                        ),
                        const Divider(height: 32),

                        // Select size
                        const Text(
                          'SELECT SIZE',
                          style: TextStyle(
                            fontSize: 9,
                            fontWeight: FontWeight.w900,
                            color: Colors.grey,
                            letterSpacing: 1.0,
                          ),
                        ),
                        const SizedBox(height: 12),
                        Row(
                          children: _sizes.map((size) {
                            final isSel = _selectedSize == size;
                            return GestureDetector(
                              onTap: () => setState(() => _selectedSize = size),
                              child: Container(
                                margin: const EdgeInsets.only(right: 12),
                                width: 44,
                                height: 44,
                                decoration: BoxDecoration(
                                  color: isSel ? themeColor : Colors.white,
                                  border: Border.all(color: isSel ? themeColor : Colors.grey.shade300),
                                  shape: BoxShape.circle,
                                ),
                                child: Center(
                                  child: Text(
                                    size,
                                    style: TextStyle(
                                      color: isSel ? Colors.white : Colors.black,
                                      fontWeight: FontWeight.bold,
                                      fontSize: 12,
                                    ),
                                  ),
                                ),
                              ),
                            );
                          }).toList(),
                        ),
                        const Divider(height: 32),

                        // Colors Selection
                        const Text(
                          'SELECT COLOR',
                          style: TextStyle(
                            fontSize: 9,
                            fontWeight: FontWeight.w900,
                            color: Colors.grey,
                            letterSpacing: 1.0,
                          ),
                        ),
                        const SizedBox(height: 12),
                        Row(
                          children: _colors.map((color) {
                            final isSel = _selectedColor == color;
                            return GestureDetector(
                              onTap: () => setState(() => _selectedColor = color),
                              child: Container(
                                margin: const EdgeInsets.only(right: 12),
                                width: 32,
                                height: 32,
                                decoration: BoxDecoration(
                                  color: color,
                                  shape: BoxShape.circle,
                                  border: Border.all(
                                    color: isSel ? Colors.grey.shade900 : Colors.transparent,
                                    width: 2.5,
                                  ),
                                ),
                              ),
                            );
                          }).toList(),
                        ),
                        const Divider(height: 32),

                        // Description
                        const Text(
                          'PRODUCT DESCRIPTION',
                          style: TextStyle(
                            fontSize: 9,
                            fontWeight: FontWeight.w900,
                            color: Colors.grey,
                            letterSpacing: 1.0,
                          ),
                        ),
                        const SizedBox(height: 12),
                        Text(
                          _mockDetails['description']!,
                          style: const TextStyle(fontSize: 11, height: 1.6, color: Colors.black54),
                        ),
                        const SizedBox(height: 40),

                      ],
                    ),
                  )
                ],
              ),
            ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          border: Border(top: BorderSide(color: Colors.grey.shade100)),
        ),
        child: Row(
          children: [
            Expanded(
              flex: 3,
              child: OutlinedButton(
                onPressed: _handleToggleWishlist,
                style: OutlinedButton.styleFrom(
                  side: BorderSide(
                    color: inWishlist ? themeColor : Colors.grey.shade300,
                    width: 1.5,
                  ),
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: Icon(
                  inWishlist ? Icons.favorite : Icons.favorite_border,
                  color: inWishlist ? themeColor : Colors.grey,
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              flex: 7,
              child: ElevatedButton(
                onPressed: _handleAddToBag,
                style: ElevatedButton.styleFrom(
                  backgroundColor: themeColor,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: const Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.shopping_bag_outlined, size: 18),
                    SizedBox(width: 8),
                    Text(
                      'ADD TO BAG',
                      style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1.0),
                    )
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
