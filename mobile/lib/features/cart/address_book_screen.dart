import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/storage/hive_storage.dart';
import '../../core/theme/app_theme.dart';

class AddressBookScreen extends StatefulWidget {
  final bool selectMode;
  const AddressBookScreen({super.key, this.selectMode = false});

  @override
  State<AddressBookScreen> createState() => _AddressBookScreenState();
}

class _AddressBookScreenState extends State<AddressBookScreen> {
  List<dynamic> _addresses = [];
  bool _showAddForm = false;

  final _nameController = TextEditingController();
  final _phoneController = TextEditingController();
  final _lineController = TextEditingController();
  final _cityController = TextEditingController();
  final _stateController = TextEditingController();
  final _zipController = TextEditingController();
  String _addressType = 'Home';

  @override
  void initState() {
    super.initState();
    _loadAddresses();
  }

  void _loadAddresses() {
    final list = HiveStorage.profile.get('addresses', defaultValue: <dynamic>[]) as List;
    if (list.isEmpty) {
      // Seed default addresses
      final seeds = [
        {
          'type': 'Home',
          'name': 'Asim Gee',
          'phone': '03001234567',
          'address_line_1': 'House 12, Block J3, Johar Town',
          'city': 'Lahore',
          'state': 'Punjab',
          'postal_code': '54782',
        },
        {
          'type': 'Office',
          'name': 'Asim Gee',
          'phone': '03009876543',
          'address_line_1': 'Floor 4, Software Park, Gulberg III',
          'city': 'Lahore',
          'state': 'Punjab',
          'postal_code': '54660',
        }
      ];
      HiveStorage.profile.put('addresses', seeds);
      setState(() {
        _addresses = seeds;
      });
    } else {
      setState(() {
        _addresses = list;
      });
    }
  }

  void _addAddress() {
    final name = _nameController.text.trim();
    final phone = _phoneController.text.trim();
    final line = _lineController.text.trim();
    final city = _cityController.text.trim();
    final state = _stateController.text.trim();
    final zip = _zipController.text.trim();

    if (name.isEmpty || phone.isEmpty || line.isEmpty || city.isEmpty || state.isEmpty || zip.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please fill all fields.')),
      );
      return;
    }

    final newAddr = {
      'type': _addressType,
      'name': name,
      'phone': phone,
      'address_line_1': line,
      'city': city,
      'state': state,
      'postal_code': zip,
    };

    final updated = List<dynamic>.from(_addresses)..add(newAddr);
    HiveStorage.profile.put('addresses', updated);
    _loadAddresses();

    // Reset Form fields
    _nameController.clear();
    _phoneController.clear();
    _lineController.clear();
    _cityController.clear();
    _stateController.clear();
    _zipController.clear();

    setState(() {
      _showAddForm = false;
    });

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('New address saved successfully!')),
    );
  }

  void _deleteAddress(int index) {
    final updated = List<dynamic>.from(_addresses)..removeAt(index);
    HiveStorage.profile.put('addresses', updated);
    _loadAddresses();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('ADDRESS BOOK'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.go(widget.selectMode ? '/checkout' : '/profile'),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            if (_showAddForm) ...[
              _buildAddAddressForm(),
              const SizedBox(height: 20),
            ] else ...[
              ElevatedButton.icon(
                onPressed: () => setState(() => _showAddForm = true),
                icon: const Icon(Icons.add, size: 16),
                label: const Text('ADD NEW ADDRESS', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900)),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.black,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                ),
              ),
              const SizedBox(height: 20),
            ],

            const Text('SAVED ADDRESSES', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey, letterSpacing: 0.5)),
            const SizedBox(height: 12),

            _addresses.isEmpty
                ? const Center(child: Padding(padding: EdgeInsets.all(24.0), child: Text('No saved addresses.')))
                : ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: _addresses.length,
                    itemBuilder: (context, index) {
                      final addr = _addresses[index];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 16),
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          border: Border.all(color: Colors.grey.shade200),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: AppTheme.primaryColor.withOpacity(0.08),
                                    borderRadius: BorderRadius.circular(4),
                                  ),
                                  child: Text(
                                    (addr['type'] ?? 'Home').toUpperCase(),
                                    style: const TextStyle(fontSize: 8, fontWeight: FontWeight.w900, color: AppTheme.primaryColor),
                                  ),
                                ),
                                Row(
                                  children: [
                                    if (widget.selectMode)
                                      TextButton(
                                        onPressed: () {
                                          HiveStorage.settings.put('selected_checkout_address', addr);
                                          context.go('/checkout');
                                        },
                                        child: const Text('SELECT', style: TextStyle(color: Colors.green, fontWeight: FontWeight.bold, fontSize: 10)),
                                      ),
                                    IconButton(
                                      icon: const Icon(Icons.delete_outline, color: Colors.grey, size: 18),
                                      onPressed: () => _deleteAddress(index),
                                    ),
                                  ],
                                )
                              ],
                            ),
                            const SizedBox(height: 12),
                            Text(
                              addr['name'] ?? '',
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              '${addr['address_line_1']}\n${addr['city']}, ${addr['state']} - ${addr['postal_code']}',
                              style: const TextStyle(fontSize: 10, color: Colors.black54, height: 1.4),
                            ),
                            const SizedBox(height: 6),
                            Text(
                              'Phone: ${addr['phone']}',
                              style: const TextStyle(fontSize: 10, color: Colors.black45, fontWeight: FontWeight.bold),
                            ),
                          ],
                        ),
                      );
                    },
                  ),
          ],
        ),
      ),
    );
  }

  Widget _buildAddAddressForm() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text('ADD NEW ADDRESS', style: TextStyle(fontWeight: FontWeight.w900, fontSize: 11)),
              IconButton(
                icon: const Icon(Icons.close, size: 18),
                onPressed: () => setState(() => _showAddForm = false),
              ),
            ],
          ),
          const SizedBox(height: 12),
          // Type selectors
          Row(
            children: ['Home', 'Office', 'Other'].map((type) {
              final isSel = _addressType == type;
              return GestureDetector(
                onTap: () => setState(() => _addressType = type),
                child: Container(
                  margin: const EdgeInsets.only(right: 12),
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  decoration: BoxDecoration(
                    color: isSel ? AppTheme.primaryColor : Colors.white,
                    border: Border.all(color: isSel ? AppTheme.primaryColor : Colors.grey.shade300),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    type,
                    style: TextStyle(color: isSel ? Colors.white : Colors.black, fontWeight: FontWeight.bold, fontSize: 10),
                  ),
                ),
              );
            }).toList(),
          ),
          const SizedBox(height: 16),

          _buildField(_nameController, 'Receiver Name'),
          _buildField(_phoneController, 'Mobile Phone Number', isPhone: true),
          _buildField(_lineController, 'Street Address line'),
          Row(
            children: [
              Expanded(child: _buildField(_cityController, 'City')),
              const SizedBox(width: 12),
              Expanded(child: _buildField(_stateController, 'State/Province')),
            ],
          ),
          _buildField(_zipController, 'Postal / ZIP Code'),
          const SizedBox(height: 12),

          ElevatedButton(
            onPressed: _addAddress,
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.primaryColor,
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(vertical: 14),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
            ),
            child: const Text('SAVE ADDRESS', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900)),
          ),
        ],
      ),
    );
  }

  Widget _buildField(TextEditingController controller, String hint, {bool isPhone = false}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12.0),
      child: TextField(
        controller: controller,
        keyboardType: isPhone ? TextInputType.phone : TextInputType.text,
        decoration: InputDecoration(
          hintText: hint,
          contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
        ),
        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
      ),
    );
  }
}
