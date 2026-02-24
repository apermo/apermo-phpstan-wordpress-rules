<?php

declare(strict_types=1);

// Should be flagged (38 chars)
register_taxonomy('extremely_long_custom_taxonomy_name_xy', 'post', []);

// Should be flagged (33 chars)
register_taxonomy('my_very_long_custom_taxonomy_tags', 'post', []);

// Should NOT be flagged (exactly 32 chars)
register_taxonomy('a_taxonomy_exactly_32_characters', 'post', []);

// Should NOT be flagged (short)
register_taxonomy('product_category', 'product', []);

// Should NOT be flagged (variable — can't check)
register_taxonomy($taxonomy, 'post', []);
