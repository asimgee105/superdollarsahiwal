import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../../core/storage/hive_storage.dart';

class CartScreen extends StatefulWidget {
  const CartScreen({super.key});

  @override
  State<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  final _couponController = TextEditingController();
  double _discount = 0.0;
  String _activeCoupon = '';

  void _applyCoupon() {
    final code = _couponController.text.trim().toUpperCase();
    if (code == 'AURA20') {
      setState(() {
        _discount = 0.20; // 20% discount
        _activeCoupon = code;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Coupon AURA20 applied! 20% discount added.')),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Invalid coupon code.')),
      );
    }
  }

  void _removeCoupon() {
    setState(() {
      _discount = 0.0;
      _activeCoupon = '';
      _couponController.clear();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('SHOPPING BAG'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/home'),
        ),
      ),
      body: ValueListenableBuilder(
        valueListenable: HiveStorage.cart.listenable(),
        builder: (context, Box box, _) {
          final items = box.values.toList();
          
          if (items.isEmpty) {
            return Center(
              child: Padding(
                padding: const EdgeInsets.all(24.0),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFF3F6C).withOpacity(0.05),
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(Icons.shopping_bag_outlined, color: Color(0xFFFF3F6C), size: 48),
                    ),
                    const SizedBox(height: 24),
                    const Text(
                      'Your Bag is Empty',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: Colors.black87),
                    ),
                    const SizedBox(height: 8),
                    const Text(
                      'Explore latest trends and add items to your shopping bag to get started.',
                      style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: Colors.grey),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 24),
                    ElevatedButton(
                      onPressed: () => context.go('/catalog'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFFFF3F6C),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      child: const Text('SHOP NOW', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900)),
                    )
                  ],
                ),
              ),
            );
          }

          // Calculate subtotal
          double subtotal = 0.0;
          for (var item in items) {
            final price = (item['price'] as num?)?.toDouble() ?? 0.0;
            final qty = (item['quantity'] as num?)?.toInt() ?? 1;
            subtotal += (price * qty);
          }

          final discountAmount = subtotal * _discount;
          const double shipping = 150.0;
          final total = subtotal - discountAmount + shipping;

          return Column(
            children: [
              Expanded(
                child: ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: items.length,
                  itemBuilder: (context, idx) {
                    final item = items[idx];
                    final id = item['id'].toString();
                    final price = (item['price'] as num?)?.toDouble() ?? 0.0;
                    final qty = (item['quantity'] as num?)?.toInt() ?? 1;

                    return Container(
                      margin: const EdgeInsets.only(bottom: 16),
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        border: Border.all(color: Colors.grey.shade200),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            width: 70,
                            height: 90,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(8),
                              image: DecorationImage(
                                image: NetworkImage(item['image'] ?? ''),
                                fit: BoxFit.cover,
                              ),
                            ),
                          ),
                          const SizedBox(width: 14),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  item['brand'] ?? '',
                                  style: const TextStyle(
                                    fontSize: 8,
                                    fontWeight: FontWeight.w900,
                                    color: Color(0xFFFF3F6C),
                                    letterSpacing: 1.0,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  item['title'] ?? '',
                                  style: const TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.black87,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                const SizedBox(height: 8),
                                Text(
                                  'Rs. ${price.toStringAsFixed(0)}',
                                  style: const TextStyle(
                                    fontSize: 13,
                                    fontWeight: FontWeight.w900,
                                    color: Colors.black,
                                  ),
                                ),
                                const SizedBox(height: 12),
                                // Quantity selector
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Row(
                                      children: [
                                        IconButton(
                                          onPressed: () {
                                            if (qty > 1) {
                                              item['quantity'] = qty - 1;
                                              box.put(id, item);
                                            } else {
                                              box.delete(id);
                                            }
                                          },
                                          icon: const Icon(Icons.remove_circle_outline, size: 20),
                                          padding: EdgeInsets.zero,
                                          constraints: const BoxConstraints(),
                                        ),
                                        Padding(
                                          padding: const EdgeInsets.symmetric(horizontal: 12.0),
                                          child: Text(
                                            qty.toString(),
                                            style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 13),
                                          ),
                                        ),
                                        IconButton(
                                          onPressed: () {
                                            item['quantity'] = qty + 1;
                                            box.put(id, item);
                                          },
                                          icon: const Icon(Icons.add_circle_outline, size: 20),
                                          padding: EdgeInsets.zero,
                                          constraints: const BoxConstraints(),
                                        ),
                                      ],
                                    ),
                                    IconButton(
                                      onPressed: () => box.delete(id),
                                      icon: const Icon(Icons.delete_outline, color: Colors.grey, size: 20),
                                    )
                                  ],
                                )
                              ],
                            ),
                          )
                        ],
                      ),
                    );
                  },
                ),
              ),

              // Bottom Pricing & Checkout Box
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  border: Border(top: BorderSide(color: Colors.grey.shade200)),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    
                    // Coupon inputs
                    if (_activeCoupon.isEmpty) ...[
                      Row(
                        children: [
                          Expanded(
                            child: TextField(
                              controller: _couponController,
                              decoration: InputDecoration(
                                hintText: 'Enter coupon code (e.g. AURA20)',
                                contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                                border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                              ),
                              style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold),
                            ),
                          ),
                          const SizedBox(width: 8),
                          ElevatedButton(
                            onPressed: _applyCoupon,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.black,
                              foregroundColor: Colors.white,
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                            ),
                            child: const Text('Apply', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900)),
                          )
                        ],
                      ),
                    ] else ...[
                      Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: const Color(0xFFFF3F6C).withOpacity(0.04),
                          border: Border.all(color: const Color(0xFFFF3F6C).withOpacity(0.2)),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              'Coupon $_activeCoupon Active',
                              style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Color(0xFFFF3F6C)),
                            ),
                            IconButton(
                              onPressed: _removeCoupon,
                              icon: const Icon(Icons.clear, size: 16, color: Color(0xFFFF3F6C)),
                              padding: EdgeInsets.zero,
                              constraints: const BoxConstraints(),
                            )
                          ],
                        ),
                      ),
                    ],
                    const SizedBox(height: 18),

                    // Price Breakdown
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('Bag Total', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.grey)),
                        Text('Rs. ${subtotal.toStringAsFixed(0)}', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900)),
                      ],
                    ),
                    const SizedBox(height: 6),
                    if (discountAmount > 0) ...[
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Discount Amount', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.grey)),
                          Text('- Rs. ${discountAmount.toStringAsFixed(0)}', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: Color(0xFFFF3F6C))),
                        ],
                      ),
                      const SizedBox(height: 6),
                    ],
                    const Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('Shipping Charge', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.grey)),
                        Text('Rs. 150', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900)),
                      ],
                    ),
                    const Divider(height: 24),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('Order Total', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w900)),
                        Text('Rs. ${total.toStringAsFixed(0)}', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: Color(0xFFFF3F6C))),
                      ],
                    ),
                    const SizedBox(height: 18),

                    ElevatedButton(
                      onPressed: () => context.go('/checkout'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFFFF3F6C),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      child: const Text('PROCEED TO CHECKOUT', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900)),
                    )

                  ],
                ),
              ),

            ],
          );
        },
      ),
    );
  }
}
