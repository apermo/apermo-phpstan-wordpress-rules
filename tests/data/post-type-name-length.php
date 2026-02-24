<?php

declare(strict_types=1);

// Should be flagged (21 chars)
register_post_type('custom_portfolio_item', []);

// Should be flagged (26 chars)
register_post_type('my_very_long_custom_type_x', []);

// Should NOT be flagged (exactly 20 chars)
register_post_type('custom_product_types', []);

// Should NOT be flagged (short)
register_post_type('portfolio', []);

// Should NOT be flagged (variable — can't check)
register_post_type($post_type, []);
