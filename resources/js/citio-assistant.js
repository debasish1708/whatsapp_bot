class CitioAssistant {
    constructor() {
        // Global state management
        this.currentState = null;
        this.isEdit = false;
        this.dataMap = new Map(); // Global hashMap to store form data
        this.chatId = null;

        // UI elements
        this.chatBody = document.getElementById('chatBody');
        this.mainActions = null;

        // Flow configurations - easily extensible for new features
        this.flowConfigs = {
            'add-sos-alert': {
                icon: '🚨',
                label: 'SOS Alert',
                apiType: 'add-sos-alert',
                hasApiSupport: true,
                instructionMessage: `📢 Create an SOS Alert in Seconds! 🚨
                    Simply provide the details in this format, and our chatbot will instantly send your alert to everyone:

                    🔹 Type (Required)
                    What kind of alert is this?
                    Example: Weather, Security, Health

                    🔹 Title (Required)
                    A short, clear headline for your alert.
                    Example: Heavy Rain Warning

                    🔹 Message (Required)
                    The full message you want to send.
                    Example: Classes are cancelled today due to heavy rainfall. Please stay safe indoors.

                    💡 Tip: Keep it short, clear, and urgent so recipients take action immediately.`,
                editPrompt: "Please provide the updated details for your SOS Alert. You can modify the Type, Title, or Message:",
                successMessage: "✅ SOS Alert sent successfully!",
                cancelMessage: "❌ SOS Alert cancelled successfully."
            },
            'add-announcement': {
                icon: '📢',
                label: 'Announcement',
                apiType: 'add-announcement',
                hasApiSupport: true,
                instructionMessage: `📢 Create a School Announcement in Seconds! 🏫

                    Simply provide the details in this format, and our system will instantly share your announcement with all students and parents:

                    🔹 Type (Required)
                    What kind of announcement is this?
                    Example: Holiday, Exam Schedule, Event Update

                    🔹 Title (Required)
                    A short, clear headline for your announcement.
                    Example: School Closed on Friday

                    🔹 Description (Required)
                    The full message you want to share.
                    Example: Due to maintenance work, the school will remain closed on Friday, 5th September 2025.

                    🔹 Start Date (Optional)
                    When does this announcement take effect?
                    Example: 4th September 2025

                    🔹 End Date (Optional)
                    When does this announcement end?
                    Example: 5th September 2025`,
                editPrompt: "Please provide the updated details for your Announcement. You can modify the Type, Title, or Message:",
                successMessage: "✅ Announcement sent successfully!",
                cancelMessage: "❌ Announcement cancelled successfully.",
                comingSoonMessage: "📢 Announcement feature is coming soon! We're working hard to bring you this functionality. Please stay tuned for updates."
            },
            'add-event': {
                icon: '📅',
                label: 'Event',
                apiType: 'add-event',
                hasApiSupport: true,
                instructionMessage: `📢 Create a School Event in Seconds! 🎓

                    Simply provide the details in this format, and our system will instantly share your event with all connected students and parents:

                    🔹 Title (Required)
                    A short, clear headline for your event.
                    Example: Annual Sports Day

                    🔹 Type (Required)
                    What kind of event is this?
                    Example: Sports, Seminar, Cultural, Workshop

                    🔹 Description (Required)
                    The full details of your event.
                    Example: Join us for three days of exciting sports competitions and fun activities.

                    🔹 Start Date (Required)
                    When does the event start?
                    Example: 10th September 2025

                    🔹 End Date (Required)
                    When does the event end?
                    Example: 12th September 2025`,
                editPrompt: "Please provide the updated details for your Event. You can modify the Type, Title, or Message:",
                successMessage: "✅ Event created successfully!",
                cancelMessage: "❌ Event cancelled successfully.",
                comingSoonMessage: "📅 Event creation feature is coming soon! We're working hard to bring you this functionality. Please stay tuned for updates."
            }
        };

        this.init();
    }

    /** Initialize chatbot */
    init() {
        this.initModalToggle();
        this.initSendHandler();
        this.initGlobalClickHandler();
        this.initInteractiveButtonHandler();
    }

    /** Toggle mobile assistant modal */
    initModalToggle() {
        const toggleBtn = document.getElementById('mobileAssistantToggle');
        const modalEl = document.getElementById('mobileAssistantModal');

        if (toggleBtn && modalEl) {
            toggleBtn.addEventListener('click', () => new bootstrap.Modal(modalEl).show());
        }
    }

    /** Handle send button and Enter key */
    initSendHandler() {
        const sendBtn = document.getElementById('maSend');
        const inputField = document.getElementById('maInput');

        if (!sendBtn || !inputField) return;

        sendBtn.addEventListener('click', () => this.handleSend());
        inputField.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.handleSend();
            }
            // Auto-grow textarea rows up to 5
            setTimeout(() => {
                inputField.style.height = 'auto';
                inputField.style.height = Math.min(inputField.scrollHeight, 120) + 'px';
            }, 0);
        });
    }

    /** Event delegation for main actions */
    initGlobalClickHandler() {
        document.addEventListener('click', e => {
            const { action } = e.target.dataset;
            if (action) {
                this.handleButtonClick(action);
            }
        });
    }

    /** Handle interactive button clicks from API responses */
    initInteractiveButtonHandler() {
        document.addEventListener('click', e => {
            const buttonId = e.target.dataset.id;
            if (buttonId) {
                this.handleInteractiveButton(buttonId, e.target.textContent.trim());
            }
        });
    }

    /** GLOBAL HOME UTILITY METHOD - All messages go through here first */
    homeUtil(message, messageType = 'text') {
        console.log('HomeUtil called with:', { message, messageType, currentState: this.currentState, isEdit: this.isEdit });

        // Handle button clicks
        if (messageType === 'button') {
            this.handleButtonLogic(message);
            return;
        }

        // Handle interactive button clicks (confirm, edit, cancel)
        if (messageType === 'interactive') {
            this.handleInteractiveLogic(message);
            return;
        }

        // Handle text messages
        if (messageType === 'text') {
            this.handleTextLogic(message);
            return;
        }
    }

    /** Handle button logic (SOS Alert, Announcement, Event buttons) */
    handleButtonLogic(buttonAction) {
        this.hideMainActions();

        const config = this.flowConfigs[buttonAction];
        if (!config) {
            console.error('Unknown button action:', buttonAction);
            return;
        }

        // Set current state
        this.currentState = buttonAction;

        // Add user message
        this.addUserMessage(`${config.icon} Create ${config.label}`);

        setTimeout(() => {
            this.showTyping();
            setTimeout(() => {
                this.hideTyping();

                // Check if API is supported
                if (!config.hasApiSupport) {
                    this.addBotMessage(config.comingSoonMessage);
                    this.resetState();
                    this.showWelcomeOptionsOnly();
                } else {
                    // Send instruction message
                    this.addBotMessage(config.instructionMessage);
                }
            }, 800);
        }, 300);
    }

    /** Handle interactive button logic (Confirm, Edit, Cancel) */
    handleInteractiveLogic(buttonText) {
        const lowerButtonText = buttonText.toLowerCase();

        if (!this.currentState || !this.dataMap.has(this.currentState)) {
            console.error('No current state or data found for interactive action');
            return;
        }

        const config = this.flowConfigs[this.currentState];

        if (lowerButtonText.includes('confirm')) {
            // Handle confirm
            this.addBotMessage(config.successMessage);
            setTimeout(() => {
                this.addBotMessage('Ready to create another alert? Select an option below:');
                this.resetState();
                this.showWelcomeOptionsOnly();
            }, 1500);

        } else if (lowerButtonText.includes('edit')) {
            // Handle edit
            this.isEdit = true;
            this.addBotMessage(config.editPrompt);

        } else if (lowerButtonText.includes('cancel')) {
            // Handle cancel
            this.addBotMessage(config.cancelMessage);
            setTimeout(() => {
                this.addBotMessage('What else would you like to do?');
                this.resetState();
                this.showWelcomeOptionsOnly();
            }, 800);
        }
    }

    /** Handle text message logic */
    handleTextLogic(message) {
        // If no current state, show welcome
        if (this.currentState === null) {
            this.showWelcomeAndOptions();
            return;
        }

        // If in edit mode
        if (this.isEdit && this.currentState && this.dataMap.has(this.currentState)) {
            this.handleEditFlow(message);
            return;
        }

        // If current state exists but no data in map yet (first time user input)
        if (this.currentState && !this.dataMap.has(this.currentState)) {
            this.handleFirstTimeInput(message);
            return;
        }

        // Default fallback
        console.log('Unhandled text logic case');
        this.showWelcomeAndOptions();
    }

    /** Handle first time user input for a flow */
    async handleFirstTimeInput(message) {
        // Store message in global map
        this.dataMap.set(this.currentState, message);

        const config = this.flowConfigs[this.currentState];

        if (config.hasApiSupport) {
            // Call API
            await this.callApi(message, this.currentState, false);
        } else {
            // For non-API supported, show coming soon
            this.addBotMessage(config.comingSoonMessage);
            this.resetState();
            this.showWelcomeOptionsOnly();
        }
    }

    /** Handle edit flow */
    async handleEditFlow(message) {
        // Update the data in map
        this.dataMap.set(this.currentState, message);

        const config = this.flowConfigs[this.currentState];

        if (config.hasApiSupport) {
            // Call API with edit flag
            await this.callApi(message, this.currentState, true);
        } else {
            this.addBotMessage(config.comingSoonMessage);
            this.resetState();
            this.showWelcomeOptionsOnly();
        }

        // Reset edit flag
        this.isEdit = false;
    }

    /** API call method */
    async callApi(message, type, isEdit) {
        const config = this.flowConfigs[type];
        const userId = window.Laravel?.userId ?? null;

        console.log(`Calling API for ${type}:`, { message, isEdit });

        try {
            const response = await $.ajax({
                url: "https://langchain.citio.cool/app2/ask",
                method: "POST",
                contentType: "application/json",
                headers: {
                    'Accept': 'application/json',
                    'x-api-key': 'TbetyveYzMDgCLb20wZh361vkR70DDHb'
                },
                data: JSON.stringify({
                    message: message,
                    user_id: userId,
                    chat_id: this.chatId,
                    type: config.apiType,
                    business_type: "school",
                    is_edit: isEdit
                })
            });

            console.log(`${config.apiType} API Response:`, response);
            this.handleApiSuccess(response, type);

        } catch (error) {
            console.error(`Error calling API for ${type}:`, error);
            this.handleApiError();
        }
    }

    /** Handle successful API response */
    handleApiSuccess(result, type) {
        const extractedData = result.extracted_data;

        if (!extractedData) {
            this.addBotMessage("Sorry, I couldn't process your request. Please try again with more details.");
            this.resetState();
            this.showWelcomeOptionsOnly();
            return;
        }

        // Store chat ID for future requests
        if (extractedData.chat_id) {
            this.chatId = extractedData.chat_id;
        }

        // Update the data in map with API response
        this.dataMap.set(type, extractedData);

        // Create interactive message with buttons
        const messageText =
            `Type: ${extractedData.type}\n` +
            `Title: ${extractedData.title}\n` +
            `Message: ${extractedData.message}\n\n` +
            `Please confirm if you want to add ${this.flowConfigs[type].label}.`;

        // Create service-specific button IDs
        const serviceUpper = type.replace('add-', '').toUpperCase();
        const chatId = extractedData.chat_id || 'null';
        const schoolId = window.Laravel?.userId ?? null;

        const buttons = [
            { id: `${schoolId}_school_${serviceUpper}_ADD_CONFIRM`, title: "✅ Confirm" },
            { id: `${schoolId}_school_${serviceUpper}_ADD_EDIT`, title: "✏️ Edit" },
            { id: `${schoolId}_school_${serviceUpper}_ADD_CANCEL`, title: "❌ Cancel" }
        ];

        this.addInteractiveBotMessage(messageText, buttons);
    }

    /** Handle API error */
    handleApiError() {
        this.addBotMessage("Sorry, there was an error processing your request. Please try again.");
        this.resetState();
        this.showWelcomeOptionsOnly();
    }

    /** Handle button clicks */
    handleButtonClick(actionType) {
        this.homeUtil(actionType, 'button');
    }

    /** Handle interactive button clicks */
    handleInteractiveButton(buttonId, buttonText) {
        console.log('Interactive button clicked:', buttonId, buttonText);

        // Add user message to show what was clicked
        this.addUserMessage(buttonText);

        setTimeout(() => {
            this.showTyping();
            setTimeout(() => {
                this.hideTyping();
                this.homeUtil(buttonText, 'interactive');
            }, 700);
        }, 300);
    }

    /** Handle message send */
    handleSend() {
        const inputField = document.getElementById('maInput');
        const message = (inputField.value || '').trim();

        if (!message) return;

        inputField.value = '';
        inputField.style.height = 'auto';
        this.addUserMessage(message);

        setTimeout(() => {
            this.showTyping();
            setTimeout(() => {
                this.hideTyping();
                this.homeUtil(message, 'text');
            }, 700);
        }, 300);
    }

    /** Reset all state */
    resetState() {
        this.currentState = null;
        this.isEdit = false;
        this.dataMap.clear();
        console.log('State reset completed');
    }

    /** Show welcome message and options */
    showWelcomeAndOptions() {
        this.addBotMessage(
            `Hello! I'm Citio, your friendly school assistant chatbot 🤖✨

I can help you quickly create and send:
• 🚨 SOS Alerts - For urgent emergency notifications (Available Now!)
• 📢 Announcements - For general school communications (Available Now!)
• 📅 Events - For upcoming activities and programs (Available Now!)

Just select an option below to get started!`
        );
        setTimeout(() => this.showWelcomeOptionsOnly(), 100);
    }

    /** Display action buttons */
    showWelcomeOptionsOnly() {
        // Remove existing actions
        const existingActions = document.getElementById('mainActions');
        if (existingActions) {
            existingActions.remove();
        }

        // Create new main actions
        this.mainActions = document.createElement('div');
        this.mainActions.id = 'mainActions';
        this.mainActions.className = 'action-buttons';

        // Generate buttons from flowConfigs
        const buttonsHTML = Object.entries(this.flowConfigs).map(([key, config]) => {
            const availabilityText = config.hasApiSupport ? '' : ' (Coming Soon)';
            const buttonClass = config.hasApiSupport ? 'action-btn' : 'action-btn coming-soon';
            return `<button class="${buttonClass}" data-action="${key}">${config.icon} Create ${config.label}${availabilityText}</button>`;
        }).join('');

        this.mainActions.innerHTML = buttonsHTML;
        this.chatBody.appendChild(this.mainActions);
        this.scrollToBottom();
    }

    /** Hide main action buttons */
    hideMainActions() {
        if (this.mainActions) {
            this.mainActions.style.display = 'none';
        }
    }

    /** Add user message bubble */
    addUserMessage(text) {
        const timestamp = this.getTimestamp();
        const wrapper = document.createElement('div');
        wrapper.innerHTML = `
            <div class="user-message">
                <div class="message-content">${this.formatMessage(text)}</div>
                <div class="message-time">${timestamp}</div>
            </div>
        `;
        this.chatBody.appendChild(wrapper);
        this.scrollToBottom();
    }

    /** Add interactive bot message with buttons */
    addInteractiveBotMessage(text, buttons) {
        const timestamp = this.getTimestamp();
        const wrapper = document.createElement('div');

        const buttonsHtml = buttons
            .map(btn => `<button class="interactive-btn" data-id="${btn.id}">${btn.title}</button>`)
            .join("");

        wrapper.innerHTML = `
            <div class="bot-message">
                <div class="message-content">
                    ${this.formatMessage(text)}
                    <div class="interactive-buttons">${buttonsHtml}</div>
                    <div class="message-time">${timestamp}</div>
                </div>
            </div>
        `;

        this.chatBody.appendChild(wrapper);
        this.scrollToBottom();
    }

    /** Add bot message bubble */
    addBotMessage(text) {
        const timestamp = this.getTimestamp();
        const wrapper = document.createElement('div');
        wrapper.innerHTML = `
            <div class="bot-message">
                <div class="message-content">
                    ${this.formatMessage(text)}
                    <div class="message-time">${timestamp}</div>
                </div>
            </div>
        `;
        this.chatBody.appendChild(wrapper);
        this.scrollToBottom();
    }

    /** Format message text */
    formatMessage(text) {
        return text
            .split('\n')
            .map(line => line.trim())
            .filter(line => line.length > 0)
            .map(line => {
                if (line.includes('**')) {
                    line = line.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                }
                return `<div>${line}</div>`;
            })
            .join('');
    }

    /** Show typing indicator */
    showTyping() {
        let typing = document.getElementById('typingIndicator');

        if (!typing) {
            typing = document.createElement('div');
            typing.id = 'typingIndicator';
            typing.className = 'typing-indicator';
            typing.innerHTML = `
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            `;
        }

        this.chatBody.appendChild(typing);
        typing.style.display = 'block';
        this.scrollToBottom();
    }

    /** Hide typing indicator */
    hideTyping() {
        const typing = document.getElementById('typingIndicator');
        if (typing) typing.style.display = 'none';
    }

    /** Scroll to bottom */
    scrollToBottom() {
        setTimeout(() => {
            this.chatBody.scrollTop = this.chatBody.scrollHeight;
        }, 100);
    }

    /** Get timestamp */
    getTimestamp() {
        return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    /** Utility: Enable API support for a service */
    enableApiSupport(serviceType, apiType = null) {
        if (this.flowConfigs[serviceType]) {
            this.flowConfigs[serviceType].hasApiSupport = true;
            if (apiType) {
                this.flowConfigs[serviceType].apiType = apiType;
            }
            console.log(`API support enabled for ${serviceType}`);
        }
    }

    /** Utility: Disable API support for a service */
    disableApiSupport(serviceType) {
        if (this.flowConfigs[serviceType]) {
            this.flowConfigs[serviceType].hasApiSupport = false;
            console.log(`API support disabled for ${serviceType}`);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.citioAssistant = new CitioAssistant();
});