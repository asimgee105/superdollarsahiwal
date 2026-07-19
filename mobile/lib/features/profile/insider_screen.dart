import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class InsiderScreen extends StatelessWidget {
  const InsiderScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('SUPER DOLLAR INSIDER CLUB'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/profile'),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            
            // Hero Loyalty Banner Card
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.black,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.grey.shade900),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: const Color(0xFFFF3F6C).withOpacity(0.15),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: const Color(0xFFFF3F6C).withOpacity(0.3)),
                    ),
                    child: const Text('LOYALTY MEMEBER', style: TextStyle(color: Color(0xFFFF3F6C), fontSize: 8, fontWeight: FontWeight.w900)),
                  ),
                  const SizedBox(height: 12),
                  const Text('450 INSIDER POINTS', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 18, letterSpacing: 0.5)),
                  const SizedBox(height: 4),
                  const Text('Exchange points for flat discounts and priority early deliveries.', style: TextStyle(color: Colors.grey, fontSize: 10, height: 1.4)),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Rewards selection
            const Text('AVAILABLE POINT EXCHANGES', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 10, color: Colors.grey, letterSpacing: 0.5)),
            const SizedBox(height: 12),

            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: Column(
                children: [
                  ListTile(
                    title: const Text('Rs. 500 Store Voucher', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                    subtitle: const Text('Costs 200 Loyalty Points', style: TextStyle(fontSize: 10, color: Colors.grey)),
                    trailing: TextButton(
                      onPressed: () {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Voucher claimed successfully! Code emailed.')),
                        );
                      },
                      child: const Text('CLAIM', style: TextStyle(color: Color(0xFFFF3F6C), fontWeight: FontWeight.bold, fontSize: 11)),
                    ),
                  ),
                  const Divider(height: 1),
                  ListTile(
                    title: const Text('Free Express Delivery Coupon', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                    subtitle: const Text('Costs 100 Loyalty Points', style: TextStyle(fontSize: 10, color: Colors.grey)),
                    trailing: TextButton(
                      onPressed: () {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Delivery coupon claimed successfully!')),
                        );
                      },
                      child: const Text('CLAIM', style: TextStyle(color: Color(0xFFFF3F6C), fontWeight: FontWeight.bold, fontSize: 11)),
                    ),
                  ),
                ],
              ),
            )

          ],
        ),
      ),
    );
  }
}
