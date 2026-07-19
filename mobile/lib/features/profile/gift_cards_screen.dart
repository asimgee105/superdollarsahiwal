import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class GiftCardsScreen extends StatefulWidget {
  const GiftCardsScreen({super.key});

  @override
  State<GiftCardsScreen> createState() => _GiftCardsScreenState();
}

class _GiftCardsScreenState extends State<GiftCardsScreen> {
  final _cardNumberController = TextEditingController();
  final _pinController = TextEditingController();
  double _retrievedBalance = 0.0;
  bool _showBalance = false;
  bool _loading = false;

  void _checkBalance() {
    final number = _cardNumberController.text.trim();
    final pin = _pinController.text.trim();
    if (number.isEmpty || pin.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please fill all card fields.')),
      );
      return;
    }
    setState(() {
      _loading = true;
      _showBalance = false;
    });

    Future.delayed(const Duration(seconds: 1), () {
      if (mounted) {
        setState(() {
          _loading = false;
          _retrievedBalance = 2500.0;
          _showBalance = true;
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('GIFT CARDS'),
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
            
            // Brand Card Promo Banner
            Container(
              padding: const EdgeInsets.all(20),
              height: 140,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFFFF3F6C), Color(0xFFFF6B8B)],
                ),
                borderRadius: BorderRadius.circular(16),
              ),
              child: const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text('AURA GIFT CARD', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w950, fontSize: 14, letterSpacing: 1.0)),
                  SizedBox(height: 6),
                  Text('Share the joy of style. The perfect gift voucher for birthdays, anniversaries, or appreciation tokens.', style: TextStyle(color: Colors.white70, fontSize: 10, height: 1.4)),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Form card
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const Text('CHECK CARD BALANCE', style: TextStyle(fontWeight: FontWeight.w950, fontSize: 10, color: Colors.grey, letterSpacing: 0.5)),
                  const SizedBox(height: 16),
                  
                  TextField(
                    controller: _cardNumberController,
                    keyboardType: TextInputType.number,
                    decoration: InputDecoration(
                      hintText: 'Enter 16-digit Card Number',
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                    style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 12),
                  
                  TextField(
                    controller: _pinController,
                    keyboardType: TextInputType.number,
                    obscureText: true,
                    decoration: InputDecoration(
                      hintText: 'Enter Card PIN',
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                    style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 16),

                  ElevatedButton(
                    onPressed: _loading ? null : _checkBalance,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.black,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                    child: Text(
                      _loading ? 'FETCHING...' : 'CHECK BALANCE',
                      style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w950),
                    ),
                  )
                ],
              ),
            ),
            const SizedBox(height: 20),

            if (_showBalance) ...[
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.green.shade50,
                  border: Border.all(color: Colors.green.shade100),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Column(
                  children: [
                    const Text('AVAILABLE CARD BALANCE', style: TextStyle(color: Colors.green, fontSize: 9, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 4),
                    Text('Rs. ${_retrievedBalance.toStringAsFixed(0)}', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w950, color: Colors.green)),
                  ],
                ),
              )
            ],

          ],
        ),
      ),
    );
  }
}
