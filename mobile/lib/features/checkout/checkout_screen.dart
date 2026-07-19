import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/storage/hive_storage.dart';

class CheckoutScreen extends StatefulWidget {
  const CheckoutScreen({super.key});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  String _selectedAddressType = 'Home';
  String _selectedPaymentMethod = 'COD';
  bool _isPlacingOrder = false;

  void _handlePlaceOrder() {
    setState(() => _isPlacingOrder = true);
    
    // Simulate placing order with API
    Future.delayed(const Duration(seconds: 2), () {
      if (mounted) {
        setState(() => _isPlacingOrder = false);
        
        // Clear local cached cart
        HiveStorage.cart.clear();

        // Show premium success dialog
        showDialog(
          context: context,
          barrierDismissible: false,
          builder: (context) {
            return AlertDialog(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              title: const Text(
                'ORDER PLACED!',
                textAlign: TextAlign.center,
                style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, letterSpacing: 1.0),
              ),
              content: const Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.check_circle_outline, color: Colors.green, size: 64),
                  SizedBox(height: 16),
                  Text(
                    'Your order ODR-849102 has been successfully placed. You will receive an email confirmation shortly.',
                    style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: Colors.grey, height: 1.5),
                    textAlign: TextAlign.center,
                  )
                ],
              ),
              actions: [
                Center(
                  child: ElevatedButton(
                    onPressed: () {
                      Navigator.pop(context); // Pop dialog
                      context.go('/orders'); // Route to orders history
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFFF3F6C),
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                    child: const Text('VIEW ORDERS', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900)),
                  ),
                )
              ],
            );
          },
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('CHECKOUT'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/cart'),
        ),
      ),
      body: _isPlacingOrder
          ? const Center(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  CircularProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(Color(0xFFFF3F6C))),
                  SizedBox(height: 16),
                  Text(
                    'PLACING YOUR ORDER...',
                    style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 1.0, color: Colors.grey),
                  ),
                ],
              ),
            )
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  
                  // Address Section
                  const Text(
                    'DELIVERY ADDRESS',
                    style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 1.0),
                  ),
                  const SizedBox(height: 12),
                  
                  Row(
                    children: [
                      Expanded(
                        child: GestureDetector(
                          onTap: () => setState(() => _selectedAddressType = 'Home'),
                          child: Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              border: Border.all(
                                color: _selectedAddressType == 'Home' ? const Color(0xFFFF3F6C) : Colors.grey.shade200,
                                width: 1.5,
                              ),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Icon(Icons.home_outlined, size: 16, color: _selectedAddressType == 'Home' ? const Color(0xFFFF3F6C) : Colors.grey),
                                    const SizedBox(width: 6),
                                    const Text('HOME', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 11)),
                                  ],
                                ),
                                const SizedBox(height: 8),
                                const Text('Asim Gee\nHouse 12, Block J3, Johar Town, Lahore', style: TextStyle(fontSize: 10, color: Colors.black54, height: 1.4)),
                              ],
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: GestureDetector(
                          onTap: () => setState(() => _selectedAddressType = 'Office'),
                          child: Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              border: Border.all(
                                color: _selectedAddressType == 'Office' ? const Color(0xFFFF3F6C) : Colors.grey.shade200,
                                width: 1.5,
                              ),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Icon(Icons.business_outlined, size: 16, color: _selectedAddressType == 'Office' ? const Color(0xFFFF3F6C) : Colors.grey),
                                    const SizedBox(width: 6),
                                    const Text('OFFICE', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 11)),
                                  ],
                                ),
                                const SizedBox(height: 8),
                                const Text('Asim Gee\nFloor 4, Software Park, Gulberg III, Lahore', style: TextStyle(fontSize: 10, color: Colors.black54, height: 1.4)),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 28),

                  // Payment Section
                  const Text(
                    'PAYMENT METHOD',
                    style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 1.0),
                  ),
                  const SizedBox(height: 12),

                  Column(
                    children: [
                      RadioListTile<String>(
                        value: 'COD',
                        groupValue: _selectedPaymentMethod,
                        title: const Text('Cash on Delivery (COD)', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                        activeColor: const Color(0xFFFF3F6C),
                        onChanged: (val) => setState(() => _selectedPaymentMethod = val!),
                      ),
                      RadioListTile<String>(
                        value: 'CARD',
                        groupValue: _selectedPaymentMethod,
                        title: const Text('Stripe Card Payment', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                        activeColor: const Color(0xFFFF3F6C),
                        onChanged: (val) => setState(() => _selectedPaymentMethod = val!),
                      ),
                      RadioListTile<String>(
                        value: 'WALLET',
                        groupValue: _selectedPaymentMethod,
                        title: const Text('JazzCash / EasyPaisa Vouchers', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                        activeColor: const Color(0xFFFF3F6C),
                        onChanged: (val) => setState(() => _selectedPaymentMethod = val!),
                      ),
                    ],
                  ),
                  const SizedBox(height: 40),

                  ElevatedButton(
                    onPressed: _handlePlaceOrder,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFFF3F6C),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 18),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    child: const Text('PLACE ORDER', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, letterSpacing: 1.2)),
                  ),

                ],
              ),
            ),
    );
  }
}
