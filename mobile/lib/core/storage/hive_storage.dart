import 'package:hive_flutter/hive_flutter.dart';

class HiveStorage {
  static Future<void> init() async {
    await Hive.initFlutter();
    
    // Open boxes for caching cart, user settings, and wishlist products
    await Hive.openBox('settings_box');
    await Hive.openBox('cart_box');
    await Hive.openBox('wishlist_box');
  }

  static Box get settings => Hive.box('settings_box');
  static Box get cart => Hive.box('cart_box');
  static Box get wishlist => Hive.box('wishlist_box');
}
