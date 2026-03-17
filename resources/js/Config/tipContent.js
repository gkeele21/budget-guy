export const tips = {
    'ready-to-assign': {
        emoji: '💰',
        title: 'What is "Ready to Assign"?',
        mascotTip: "Think of it like cash sitting on your kitchen table. It's real money you have, but you haven't decided what it's for yet.",
        content: `<p><strong>Ready to Assign</strong> shows how much money you have that hasn't been assigned to a category yet.</p><p>It's calculated as:</p>`,
        formula: { lines: ['+ All your income', '− Everything you\'ve assigned'], result: '= Ready to Assign' },
        warning: { title: 'If it\'s negative...', text: 'You\'ve assigned more money than you actually have. Go to your categories and reduce some amounts until Ready to Assign is $0 or more.' },
        relatedTips: ['how-envelopes-work', 'overspending']
    },
    'how-envelopes-work': {
        emoji: '📦',
        title: 'How do envelopes work?',
        mascotTip: "Imagine putting cash into labeled envelopes — one for groceries, one for rent, one for fun. That's exactly what Budget Guy does, but digitally!",
        content: `<p>Each <strong>category</strong> is like an envelope that holds money for a specific purpose.</p><p>When you <strong>assign</strong> money to a category, you're putting cash into that envelope. When you <strong>spend</strong>, the money comes out of the envelope.</p><p>The <strong>Available</strong> column shows how much is left in each envelope. If it hits zero, the envelope is empty — you've spent everything you planned for that category.</p>`,
        formula: { lines: ['+ Amount assigned (budgeted)', '− Amount spent'], result: '= Available (what\'s left)' },
        warning: null,
        relatedTips: ['ready-to-assign', 'overspending', 'move-money']
    },
    'overspending': {
        emoji: '🔴',
        title: 'What if I overspend?',
        mascotTip: "Don't panic! Overspending happens. The important thing is to deal with it right away instead of ignoring it.",
        content: `<p>When a category's <strong>Available</strong> goes negative (shown in red), it means you've spent more than you put in that envelope.</p><p>To fix it, you have two options:</p><p><strong>1. Move money from another category</strong> — take from a category that has extra and cover the overspending.</p><p><strong>2. Assign more money</strong> — if you have Ready to Assign available, put more into the overspent category.</p><p>The key is to make a conscious decision about where the extra money comes from.</p>`,
        formula: null,
        warning: { title: 'Don\'t ignore it', text: 'Unhandled overspending rolls into next month as negative Available. It\'s better to move money and acknowledge the overspend now.' },
        relatedTips: ['move-money', 'how-envelopes-work']
    },
    'budgeted-vs-available': {
        emoji: '🔢',
        title: 'Budgeted vs. Available',
        mascotTip: "Budgeted is what you planned. Available is what's actually left. They start the same, but diverge as you spend!",
        content: `<p><strong>Assigned (Budgeted)</strong> is the amount you put into the envelope this month — your plan.</p><p><strong>Available</strong> is what's actually left to spend — your reality.</p><p>At the start of the month they're the same. As you record transactions, Available goes down while Budgeted stays the same.</p>`,
        formula: { lines: ['Assigned this month: $500', 'Spent so far: −$127'], result: 'Available: $373' },
        warning: null,
        relatedTips: ['how-envelopes-work', 'ready-to-assign']
    },
    'move-money': {
        emoji: '🔄',
        title: 'Moving money between categories',
        mascotTip: "Life changes, and so can your budget! Moving money between categories is totally normal — it means you're adapting your plan to reality.",
        content: `<p>Sometimes you need to move money from one category to another — maybe you overspent on dining out but have extra in your clothing budget.</p><p>To move money: tap the category you want to take FROM, reduce its assigned amount, then tap the category you want to add TO and increase its amount.</p><p>The key rule: <strong>Ready to Assign should stay at $0</strong>. If you reduce one category, increase another by the same amount.</p>`,
        formula: null,
        warning: null,
        relatedTips: ['overspending', 'how-envelopes-work']
    },
    'transfers': {
        emoji: '↔️',
        title: 'Transfers between accounts',
        mascotTip: "Transfers move money between your accounts — like moving cash from checking to savings. It doesn't change your budget, just where the money lives!",
        content: `<p>A <strong>transfer</strong> moves money between two of your accounts. Unlike regular transactions, transfers don't affect your category budgets.</p><p>Common transfers: checking → savings, bank → credit card payment.</p><p>When you create a transfer, Budget Guy automatically creates matching transactions in both accounts.</p>`,
        formula: null,
        warning: { title: 'Credit card payments', text: 'When you pay your credit card from checking, record it as a transfer. The spending already came out of your categories when you made the purchases.' },
        relatedTips: ['credit-cards']
    },
    'entering-transactions': {
        emoji: '📝',
        title: 'Entering transactions',
        mascotTip: "Record purchases as they happen — it only takes a few seconds and keeps your budget accurate!",
        content: `<p>Tap the <strong>+</strong> button on the Transactions page (or <strong>+ Transaction</strong> on the Budget page) to record a purchase.</p><p>Fill in the basics:</p><ul><li><strong>Type</strong> — Expense, Income, or Transfer</li><li><strong>Date</strong> — defaults to today</li><li><strong>Account</strong> — which account the money came from</li><li><strong>Payee</strong> — who you paid (Budget Guy remembers payees and can auto-fill the category next time!)</li><li><strong>Amount</strong> — how much you spent or received</li><li><strong>Category</strong> — which envelope the money comes out of</li></ul><p>The key habit is recording transactions <strong>as they happen</strong>. The more current your budget, the better your spending decisions.</p>`,
        formula: null,
        warning: null,
        guidedTour: { track: 'transactions', label: 'Try the Add Transaction Tour' },
        relatedTips: ['split-transactions', 'how-envelopes-work', 'voice-transactions']
    },
    'split-transactions': {
        emoji: '✂️',
        title: 'Splitting a transaction',
        mascotTip: "One receipt, multiple categories? Splits let you divide a single transaction across different envelopes!",
        content: `<p>Sometimes a single purchase spans multiple categories — like a Target run where you buy groceries AND a birthday gift.</p><p>To split a transaction:</p><ol><li>Tap the <strong>Category</strong> field and choose <strong>"Split Transaction..."</strong></li><li>Add categories and amounts for each part of the purchase</li><li>The <strong>Remaining</strong> bar shows when your splits are balanced</li><li>Tap <strong>Save Split</strong> when the remaining amount hits $0</li></ol><p>You can even <strong>mix income & expenses</strong> in a single split — useful for things like refunds on a larger purchase.</p>`,
        formula: { lines: ['Target run total: $85.00', 'Groceries: $52.00', 'Gift (Shopping): $33.00'], result: 'Remaining: $0.00 ✓' },
        warning: null,
        guidedTour: { track: 'splits', label: 'Try the Split Transaction Tour' },
        relatedTips: ['entering-transactions', 'how-envelopes-work']
    },
    'recurring-transactions': {
        emoji: '🔁',
        title: 'Setting up recurring transactions',
        mascotTip: "Set it and forget it! Recurring transactions help you remember bills and plan ahead.",
        content: `<p><strong>Recurring transactions</strong> are bills or income that repeat on a schedule — rent, subscriptions, paychecks, etc.</p><p>When you set one up, Budget Guy will show upcoming expected transactions in your Plan view and can auto-create them when they're due.</p>`,
        formula: null,
        warning: null,
        guidedTour: { track: 'recurring', label: 'Try the Recurring Bills Tour' },
        relatedTips: ['plan-projections']
    },
    'voice-transactions': {
        emoji: '🎤',
        title: 'Using voice to add transactions',
        mascotTip: "Just say what you spent! Like 'spent 45 dollars at Target for groceries' and I'll fill everything in.",
        content: `<p>Budget Guy has a <strong>voice feature</strong> that lets you dictate transactions naturally.</p><p>Tap the microphone button and say something like:</p><p><em>"Spent $45 at Target for groceries"</em></p><p>Budget Guy will parse the amount, payee, and category automatically. Review the details and tap save!</p>`,
        formula: null,
        warning: null,
        relatedTips: []
    },
    'plan-projections': {
        emoji: '📊',
        title: 'Using the Plan view',
        mascotTip: "The Plan view is your crystal ball — set your income, project your spending, and see if your budget balances before the month even starts!",
        content: `<p>The <strong>Plan</strong> tab helps you build a spending plan for the month ahead.</p><p><strong>How it works:</strong></p><ol><li><strong>Set your income</strong> — enter your expected monthly income at the top</li><li><strong>Set projections</strong> — enter how much you plan to assign to each category</li><li><strong>Check "Left to Allocate"</strong> — this shows Income minus Projections. Green means you have room; red means you've over-planned</li></ol><p><strong>Handy shortcuts</strong> (in the menu):</p><ul><li><strong>Copy Defaults to Projections</strong> — pre-fill from your saved defaults</li><li><strong>Apply Projections to Budget</strong> — push your plan straight into this month's budget</li><li><strong>Save Projections as Defaults</strong> — save for reuse next month</li></ul><p>The Plan page is perfect for the start of each month — map out your spending, then apply it all at once.</p>`,
        formula: { lines: ['Expected income: $4,200', 'Total projected: $4,100'], result: 'Left to allocate: $100' },
        warning: null,
        guidedTour: { track: 'plan', label: 'Try the Plan Budget Tour' },
        relatedTips: ['recurring-transactions', 'ready-to-assign']
    },
    'credit-cards': {
        emoji: '💳',
        title: 'How to handle credit cards',
        mascotTip: "Credit cards can be tricky! The key is: the spending hits your category when you swipe, not when you pay the bill.",
        content: `<p>Add your credit card as an account with a <strong>negative balance</strong> (what you owe).</p><p>When you buy something with a credit card, record the transaction against the credit card account and assign it a category. The money comes out of the category envelope immediately.</p><p>When you pay your credit card from checking, record it as a <strong>transfer</strong> — the spending already happened in your categories.</p>`,
        formula: null,
        warning: { title: 'Starting balance', text: 'Enter your current credit card balance as a negative number (e.g., -$2,340). This represents what you owe.' },
        relatedTips: ['transfers']
    }
};
