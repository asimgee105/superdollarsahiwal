import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/theme/app_theme.dart';

class TrackOrderScreen extends StatefulWidget {
  final String orderId;
  const TrackOrderScreen({super.key, required this.orderId});

  @override
  State<TrackOrderScreen> createState() => _TrackOrderScreenState();
}

class _TrackOrderScreenState extends State<TrackOrderScreen> {
  final List<Map<String, dynamic>> _stages = [
    {
      'title': 'Order Placed',
      'subtitle': 'We have received your order request.',
      'time': 'July 19, 09:30 AM',
      'done': true,
    },
    {
      'title': 'Payment Verified',
      'subtitle': 'Stripe transaction cleared successfully.',
      'time': 'July 19, 09:32 AM',
      'done': true,
    },
    {
      'title': 'Processing & Packing',
      'subtitle': 'Items gathered and packed at Lahore warehouse.',
      'time': 'July 19, 02:40 PM',
      'done': true,
    },
    {
      'title': 'Shipped',
      'subtitle': 'Handed over to courier. Tracking ID: TCS-891024.',
      'time': 'July 20, 08:00 AM',
      'done': false,
    },
    {
      'title': 'Out for Delivery',
      'subtitle': 'Courier agent is enroute to your address.',
      'time': 'Expected today',
      'done': false,
    }
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: Text('TRACK ORDER ${widget.orderId}'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go('/orders'),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Order summary card
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: Row(
                children: [
                  const Icon(Icons.local_shipping_outlined, color: AppTheme.primaryColor, size: 28),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Estimated Delivery', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey)),
                        const SizedBox(height: 4),
                        Text(
                          'TODAY BY 06:00 PM',
                          style: TextStyle(fontWeight: FontWeight.w900, fontSize: 13, color: Colors.green.shade800),
                        ),
                      ],
                    ),
                  ),
                  TextButton(
                    onPressed: () => context.go('/invoice', extra: widget.orderId),
                    child: const Text('VIEW INVOICE', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: AppTheme.primaryColor)),
                  )
                ],
              ),
            ),
            const SizedBox(height: 28),

            const Text('DELIVERY TIMELINE', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 0.5)),
            const SizedBox(height: 20),

            // Stepper timeline list
            ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: _stages.length,
              itemBuilder: (context, index) {
                final stage = _stages[index];
                final done = stage['done'] as bool;
                final isLast = index == _stages.length - 1;

                return Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Column 1: Step Dot + Line
                    Column(
                      children: [
                        Container(
                          width: 20,
                          height: 20,
                          decoration: BoxDecoration(
                            color: done ? AppTheme.primaryColor : Colors.white,
                            border: Border.all(
                              color: done ? AppTheme.primaryColor : Colors.grey.shade300,
                              width: 2,
                            ),
                            shape: BoxShape.circle,
                          ),
                          child: done
                              ? const Icon(Icons.check, size: 10, color: Colors.white)
                              : null,
                        ),
                        if (!isLast)
                          Container(
                            width: 2,
                            height: 60,
                            color: done ? AppTheme.primaryColor : Colors.grey.shade200,
                          ),
                      ],
                    ),
                    const SizedBox(width: 16),

                    // Column 2: Status Content details
                    Expanded(
                      child: Padding(
                        padding: const EdgeInsets.only(top: 2.0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              stage['title'],
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w900,
                                color: done ? Colors.black87 : Colors.grey,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              stage['subtitle'],
                              style: const TextStyle(fontSize: 10, color: Colors.grey, height: 1.4),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              stage['time'],
                              style: TextStyle(
                                fontSize: 9,
                                fontWeight: FontWeight.bold,
                                color: done ? AppTheme.primaryColor : Colors.grey.shade400,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                );
              },
            ),

            const SizedBox(height: 32),
            ElevatedButton(
              onPressed: () {
                context.go('/order-actions', extra: widget.orderId);
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.black,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text('CANCEL OR RETURN REQUEST', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
            ),
          ],
        ),
      ),
    );
  }
}
