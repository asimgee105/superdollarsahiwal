import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class OrdersScreen extends StatelessWidget {
  const OrdersScreen({super.key});

  final List<Map<String, dynamic>> _orders = const [
    {
      'id': 'ODR-849102',
      'date': 'July 19, 2026',
      'status': 'Delivered',
      'total': '4,500',
      'items': [
        {
          'title': 'Premium Cotton Kurta Set',
          'brand': 'SD PREMIUM',
          'image': 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=150&auto=format&fit=crop'
        }
      ]
    },
    {
      'id': 'ODR-748192',
      'date': 'June 28, 2026',
      'status': 'Delivered',
      'total': '2,200',
      'items': [
        {
          'title': 'Slim Fit Casual Denim Jeans',
          'brand': 'SD MEN',
          'image': 'https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=150&auto=format&fit=crop'
        }
      ]
    }
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('ORDER HISTORY'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/profile'),
        ),
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: _orders.length,
        itemBuilder: (context, idx) {
          final order = _orders[idx];
          return Container(
            margin: const EdgeInsets.only(bottom: 16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Colors.grey.shade200),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                
                // Header block
                Container(
                  padding: const EdgeInsets.all(12),
                  color: Colors.grey.shade50,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('ORDER ${order['id']}', style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 10, letterSpacing: 0.5)),
                          const SizedBox(height: 2),
                          Text('Placed: ${order['date']}', style: const TextStyle(color: Colors.grey, fontSize: 9, fontWeight: FontWeight.bold)),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.green.shade50,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: Colors.green.shade100),
                        ),
                        child: Text(
                          order['status'].toUpperCase(),
                          style: TextStyle(color: Colors.green.shade800, fontSize: 8, fontWeight: FontWeight.w900),
                        ),
                      ),
                    ],
                  ),
                ),

                // Items list
                Padding(
                  padding: const EdgeInsets.all(12.0),
                  child: Column(
                    children: (order['items'] as List).map((item) {
                      return Row(
                        children: [
                          Container(
                            width: 50,
                            height: 65,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(8),
                              image: DecorationImage(
                                image: NetworkImage(item['image']!),
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
                                  item['brand']!,
                                  style: const TextStyle(fontSize: 7, fontWeight: FontWeight.w900, color: Color(0xFFFF3F6C)),
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  item['title']!,
                                  style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: Colors.black87),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                const SizedBox(height: 4),
                                const Text('Qty: 1', style: TextStyle(color: Colors.grey, fontSize: 9)),
                              ],
                            ),
                          )
                        ],
                      );
                    }).toList(),
                  ),
                ),

                // Footer stats
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    border: Border(top: BorderSide(color: Colors.grey.shade100)),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Total Paid: Rs. ${order['total']}',
                        style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900),
                      ),
                      const Text(
                        'Track Order &rarr;',
                        style: TextStyle(color: Color(0xFFFF3F6C), fontSize: 9, fontWeight: FontWeight.w900),
                      ),
                    ],
                  ),
                ),

              ],
            ),
          );
        },
      ),
    );
  }
}
