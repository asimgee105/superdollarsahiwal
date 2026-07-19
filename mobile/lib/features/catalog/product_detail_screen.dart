import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../../core/storage/hive_storage.dart';

class ProductDetailScreen extends StatefulWidget {
  final int productId;
  const ProductDetailScreen({super.key, required this.productId});

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  final List<String> _sizes = ['S', 'M', 'L', 'XL'];
  final List<Color> _colors = [Colors.red, Colors.blue, Colors.black, Colors.indigo];

  String _selectedSize = 'M';
  Color _selectedColor = Colors.black;

  // Mock details matching static products
  final Map<String, dynamic> _mockDetails = {
    'title': 'Aura Premium Cotton Kurta Set',
    'brand': 'AURA PREMIUM',
    'price': 4500,
    'image': 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=400&auto=format&fit=crop',
    'description': 'This premium ethnic wear set features a high-density cotton weave design with detailed embroidery along the collar, paired with straight trousers for optimal everyday wear.',
  };

  void _handleAddToBag() {
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
    final wishlistBox = HiveStorage.wishlist;
    final id = widget.productId.toString();

    if (wishlistBox.containsKey(id)) {
      wishlistBox.delete(id);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Removed from wishlist.')),
      );
    } else {
      wishlistBox.put(id, {
        'id': id,
        'title': _mockDetails['title'],
        'brand': _mockDetails['brand'],
        'price': _mockDetails['price'],
        'image': _mockDetails['image'],
      });
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Saved to wishlist!')),
      );
    }
    setState(() {});
  }

  void _showAiSummary() {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (context) {
        return Container(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: const Color(0xFFFF3F6C).withOpacity(0.1),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(Icons.psychology, color: Color(0xFFFF3F6C), size: 24),
                  ),
                  const SizedBox(width: 12),
                  const Text(
                    'AI PRODUCT INSIGHTS',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w900,
                      letterSpacing: 1.2,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 18),
              const Text(
                'Based on 124 customer reviews, this product is highly praised for its premium quality cotton fabric and embroidery workmanship. 94% of buyers reported the sizing fits exactly as expected. Ideal for hot summer days due to its airy weave.',
                style: TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w600,
                  height: 1.6,
                  color: Colors.black87,
                ),
              ),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: () => Navigator.pop(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFFF3F6C),
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: const Text('GOT IT', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900)),
              )
            ],
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final inWishlist = HiveStorage.wishlist.containsKey(widget.productId.toString());

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('PRODUCT DETAILS'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/catalog'),
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            
            // Image Carousel Container
            Container(
              height: 380,
              width: double.infinity,
              decoration: BoxDecoration(
                image: DecorationImage(
                  image: NetworkImage(_mockDetails['image']!),
                  fit: BoxFit.cover,
                ),
              ),
            ),

            Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  
                  // Brand & AI insights row
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        _mockDetails['brand']!,
                        style: const TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.w900,
                          color: Color(0xFFFF3F6C),
                          letterSpacing: 1.5,
                        ),
                      ),
                      GestureDetector(
                        onTap: _showAiSummary,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                          decoration: BoxDecoration(
                            color: const Color(0xFFFF3F6C).withOpacity(0.06),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: const Color(0xFFFF3F6C).withOpacity(0.2)),
                          ),
                          child: const Row(
                            children: [
                              Icon(Icons.psychology, size: 14, color: Color(0xFFFF3F6C)),
                              SizedBox(width: 4),
                              Text(
                                'AI Summary',
                                style: TextStyle(
                                  fontSize: 8,
                                  fontWeight: FontWeight.w900,
                                  color: Color(0xFFFF3F6C),
                                ),
                              )
                            ],
                          ),
                        ),
                      )
                    ],
                  ),
                  const SizedBox(height: 6),

                  // Title
                  Text(
                    _mockDetails['title']!,
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w900,
                      color: Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 8),

                  // Price
                  Text(
                    'Rs. ${_mockDetails['price'].toString()}',
                    style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w900,
                      color: Colors.black,
                    ),
                  ),
                  const SizedBox(height: 18),

                  // Size selection
                  const Text(
                    'SELECT SIZE',
                    style: TextStyle(
                      fontSize: 9,
                      fontWeight: FontWeight.w900,
                      color: Colors.grey,
                      letterSpacing: 1.0,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: _sizes.map((sz) {
                      final selected = _selectedSize == sz;
                      return GestureDetector(
                        onTap: () => setState(() => _selectedSize = sz),
                        child: Container(
                          margin: const EdgeInsets.only(right: 12),
                          width: 42,
                          height: 42,
                          decoration: BoxDecoration(
                            border: Border.all(
                              color: selected ? const Color(0xFFFF3F6C) : Colors.grey.shade300,
                              width: 1.5,
                            ),
                            shape: BoxShape.circle,
                            color: selected ? const Color(0xFFFF3F6C).withOpacity(0.04) : Colors.transparent,
                          ),
                          child: Center(
                            child: Text(
                              sz,
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w900,
                                color: selected ? const Color(0xFFFF3F6C) : Colors.black87,
                              ),
                            ),
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                  const SizedBox(height: 24),

                  // Description details
                  const Text(
                    'PRODUCT DESCRIPTION',
                    style: TextStyle(
                      fontSize: 9,
                      fontWeight: FontWeight.w900,
                      color: Colors.grey,
                      letterSpacing: 1.0,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    _mockDetails['description']!,
                    style: const TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      height: 1.6,
                      color: Colors.black54,
                    ),
                  ),
                  const SizedBox(height: 20),

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
          border: Border(top: BorderSide(color: Colors.grey.shade200)),
        ),
        child: Row(
          children: [
            Expanded(
              flex: 3,
              child: OutlinedButton(
                onPressed: _handleToggleWishlist,
                style: OutlinedButton.styleFrom(
                  side: BorderSide(
                    color: inWishlist ? const Color(0xFFFF3F6C) : Colors.grey.shade300,
                    width: 1.5,
                  ),
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: Icon(
                  inWishlist ? Icons.favorite : Icons.favorite_border,
                  color: inWishlist ? const Color(0xFFFF3F6C) : Colors.grey,
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              flex: 7,
              child: ElevatedButton(
                onPressed: _handleAddToBag,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFFF3F6C),
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
