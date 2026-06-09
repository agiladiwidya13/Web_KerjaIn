/**
 * Material Icons Helper
 * Utility function for rendering Material Icons consistently
 */

/**
 * Create a Material Icon element
 * @param {string} name - Material Icon name (e.g., 'code', 'palette', 'star')
 * @param {string} cssClass - Additional CSS classes (optional)
 * @returns {string} - HTML string for the icon
 */
function matIcon(name, cssClass = '') {
    const classes = cssClass ? ` ${cssClass}` : '';
    return `<span class="material-icons${classes}">${name}</span>`;
}

/**
 * Create a Material Icon with size modifier
 * @param {string} name - Material Icon name
 * @param {string} size - Size: 'sm' (18px), 'md' (24px), 'lg' (36px), 'xl' (48px)
 * @returns {string} - HTML string for the icon
 */
function matIconSize(name, size = 'md') {
    const sizeMap = {
        'sm': 'md-18',
        'md': 'md-24',
        'lg': 'md-36',
        'xl': 'md-48'
    };
    const sizeClass = sizeMap[size] || 'md-24';
    return `<span class="material-icons ${sizeClass}">${name}</span>`;
}

/**
 * Emoji to Material Icon mapping
 * Used to replace emojis throughout the app
 */
const EMOJI_TO_ICON = {
    // Technology & Skills
    '💻': 'code',
    '🎨': 'palette',
    '🔧': 'build',
    '⚙️': 'settings',
    
    // Finance & Business
    '🏦': 'account_balance',
    '💰': 'attach_money',
    '📊': 'bar_chart',
    '📈': 'trending_up',
    '🏢': 'business',
    
    // Marketing & Communication
    '📢': 'campaign',
    '📝': 'description',
    '📋': 'list_alt',
    '📄': 'description',
    
    // Education & Programs
    '📚': 'school',
    '📖': 'menu_book',
    '🎓': 'school',
    
    // Status & Action
    '✅': 'check_circle',
    '⏳': 'hourglass_empty',
    '❌': 'cancel',
    '🔄': 'refresh',
    
    // Gamification
    '⭐': 'star',
    '✨': 'auto_awesome',
    '🏆': 'emoji_events',
    '🥇': 'emoji_events',
    '🥈': 'emoji_events',
    '🥉': 'emoji_events',
    '🎯': 'bullseye',
    '🏅': 'military_tech',
    
    // User & Profile
    '👤': 'account_circle',
    '👥': 'group',
    '👨‍🏫': 'person',
    '👋': 'waving_hand',
    '🔐': 'lock',
    
    // UI Elements
    '🔔': 'notifications',
    '🔍': 'search',
    '🌙': 'dark_mode',
    '☀️': 'light_mode',
    '➡️': 'arrow_forward',
    '⬅️': 'arrow_back',
    '▶': 'play_arrow',
    '◀': 'arrow_back',
    
    // Status & Messages
    '📭': 'mail_outline',
    '💼': 'work',
    '📜': 'card_membership',
    '🚀': 'rocket',
    '🆕': 'new_releases',
    '📅': 'calendar_today',
    '📱': 'phone_android',
};

/**
 * Convert emoji to Material Icon HTML
 * @param {string} emoji - Emoji character
 * @returns {string} - Material Icon HTML or original emoji if no mapping found
 */
function getIconForEmoji(emoji) {
    const iconName = EMOJI_TO_ICON[emoji];
    return iconName ? matIcon(iconName) : emoji;
}
