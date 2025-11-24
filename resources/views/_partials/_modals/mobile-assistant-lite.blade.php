{{-- resources/views/_partials/_modals/mobile-assistant-lite.blade.php --}}

<style>
  /* ===== Configuration Variables ===== */
  :root {
      /* Change these values to adjust position */
      --assistant-position-bottom: 20px;
      --assistant-position-right: 20px;
      --assistant-position-left: auto; /* Set to 20px for left side, auto for right side */

      /* Modal position - change values to adjust modal position */
      --modal-align: flex-start; /* flex-start (left), center (center), flex-end (right) */
      --modal-margin-left: 20px;
      --modal-margin-right: auto;
  }

  /* ===== Floating Assistant Button ===== */
  .floating-assistant {
      position: fixed;
      bottom: var(--assistant-position-bottom);
      right: var(--assistant-position-right);
      left: var(--assistant-position-left);
      z-index: 1050; /* Reduced from 1000 to standard Bootstrap modal z-index + 10 */
  }

  .assistant-btn {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #128c7e 0%, #0d6efd 100%);
      border: none;
      color: white;
      font-size: 24px;
      cursor: pointer;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15), 0 2px 10px rgba(18,140,126,0.3);
      transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
      pointer-events: auto;
      z-index: 1051; /* Reduced and made specific */
  }

  .assistant-btn:hover {
      transform: scale(1.1);
      box-shadow: 0 6px 25px rgba(0,0,0,0.2), 0 4px 15px rgba(18,140,126,0.4);
  }

  .assistant-btn:active {
      transform: scale(0.95);
  }

  /* Pulse animation for notification */
  .assistant-btn.has-notification::before {
      content: '';
      position: absolute;
      top: -2px;
      right: -2px;
      width: 20px;
      height: 20px;
      background: #ff4757;
      border-radius: 50%;
      border: 3px solid white;
      animation: pulse 2s infinite;
  }

  @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(255, 71, 87, 0.7); }
      70% { box-shadow: 0 0 0 10px rgba(255, 71, 87, 0); }
      100% { box-shadow: 0 0 0 0 rgba(255, 71, 87, 0); }
  }

  /* ===== Enhanced Modal Styles - Scoped to citio-ma ===== */
  .citio-ma {
      z-index: 1055; /* Reduced from 9999 to standard Bootstrap range */
  }

  .citio-ma .modal-backdrop {
      background-color: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(4px);
      z-index: 1054; /* Reduced accordingly */
  }

  .citio-ma .modal-dialog {
      max-width: 340px;
      margin: 20px auto;
      height: calc(100vh - 40px);
      max-height: 600px;
      position: relative;
      z-index: 1055;
  }

  .citio-ma .modal-content {
      height: 100%;
      border: none;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 25px 50px rgba(0,0,0,0.25);
  }

  .citio-ma .phone-container {
      width: 100%;
      height: 100%;
      border-radius: 20px;
      background: #f5f5f5;
      overflow: hidden;
      position: relative;
      display: flex;
      flex-direction: column;
  }

  /* ===== Chat Header ===== */
  .citio-ma .chat-header {
      background: linear-gradient(135deg, #128c7e 0%, #0d6efd 100%);
      color: #fff;
      padding: 16px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: relative;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      flex-shrink: 0;
  }

  .citio-ma .chat-header-info {
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 0;
  }

  .citio-ma .chat-avatar {
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 16px;
      backdrop-filter: blur(10px);
      flex-shrink: 0;
  }

  .citio-ma .chat-header-text {
      min-width: 0;
      flex: 1;
  }

  .citio-ma .chat-title {
      font-weight: 600;
      font-size: 16px;
      margin: 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 200px;
  }

  .citio-ma .chat-status {
      font-size: 12px;
      opacity: 0.8;
      margin: 0;
      white-space: nowrap;
  }

  .citio-ma .close-btn {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      color: white;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s;
      flex-shrink: 0;
  }

  .citio-ma .close-btn:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: scale(1.1);
  }

  /* ===== Chat Body ===== */
  .citio-ma .chat-body {
      flex: 1;
      background: linear-gradient(to bottom, #ece5dd 0%, #d9d0c7 100%);
      padding: 20px 16px;
      overflow-y: auto;
      position: relative;
      display: flex;
      flex-direction: column;
      gap: 16px;
      min-height: 0;
  }

  .citio-ma .chat-body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="chat-bg" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23chat-bg)"/></svg>');
      opacity: 0.3;
      pointer-events: none;
  }

  /* ===== Message Styles ===== */
  .citio-ma .message {
      position: relative;
      z-index: 1;
      animation: slideIn 0.3s ease-out;
      margin-bottom: 0;
  }

  @keyframes slideIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
  }

  .citio-ma .bot-message {
      max-width: 85%;
      align-self: flex-start;
      display: flex;
      flex-direction: column;
  }

  .citio-ma .bot-message .message-content {
      background: #fff;
      border-radius: 18px 18px 18px 4px;
      padding: 12px 16px;
      color: #303030;
      font-size: 14px;
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans", sans-serif;
      line-height: 1.5;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      position: relative;
      word-wrap: break-word;
      overflow-wrap: break-word;
      hyphens: auto;
  }

  .citio-ma .bot-message .message-time {
      font-size: 11px;
      opacity: 0.6;
      margin-top: 4px;
      font-weight: 400;
      text-align: left;
  }

  /* ===== User Message Styles ===== */
  .citio-ma .user-message {
      max-width: 85%;
      align-self: flex-end;
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      text-align: right;
      margin-left: auto;
  }

  .citio-ma .user-message .message-content {
      background: linear-gradient(135deg, #dcf8c6 0%, #d4edda 100%);
      color: #1f2937;
      border-radius: 18px 18px 4px 18px;
      padding: 12px 16px;
      font-size: 14px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      word-wrap: break-word;
      overflow-wrap: break-word;
      hyphens: auto;
      display: inline-block;
      text-align: left;
  }

  .citio-ma .user-message .message-time {
      font-size: 11px;
      opacity: 0.6;
      margin-top: 4px;
      font-weight: 400;
      text-align: right;
  }

  /* ===== Action Buttons ===== */
  .citio-ma .action-buttons {
      display: flex;
      gap: 8px;
      margin-top: 12px;
      flex-wrap: wrap;
      align-self: flex-start;
  }

  .citio-ma .action-btn {
      background: rgba(255, 255, 255, 0.9);
      color: #128c7e;
      border: 1px solid rgba(18, 140, 126, 0.2);
      border-radius: 20px;
      padding: 8px 16px;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.2s;
      font-weight: 500;
  }

  .citio-ma .action-btn:hover {
      background: #128c7e;
      color: white;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(18, 140, 126, 0.3);
  }

  /* ===== Form Styles ===== */
  .citio-ma .form-container {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 16px;
      padding: 16px;
      margin: 8px 0;
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  }

  .citio-ma .form-field {
      margin-bottom: 16px;
  }

  .citio-ma .form-field label {
      font-weight: 600;
      color: #128c7e;
      margin-bottom: 6px;
      display: block;
      font-size: 13px;
  }

  .citio-ma .form-field input,
  .citio-ma .form-field textarea {
      width: 100%;
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      padding: 10px 12px;
      font-size: 14px;
      transition: border-color 0.2s;
      background: rgba(255, 255, 255, 0.8);
      box-sizing: border-box;
  }

  .citio-ma .form-field input:focus,
  .citio-ma .form-field textarea:focus {
      outline: none;
      border-color: #128c7e;
      box-shadow: 0 0 0 3px rgba(18, 140, 126, 0.1);
  }

  /* ===== Confirm Section ===== */
  .citio-ma .confirm-section {
      background: #fff3cd;
      border: 1px solid #ffeaa7;
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 12px;
      max-width: 90%;
  }

  .citio-ma .confirm-buttons {
      display: flex;
      gap: 8px;
      margin-top: 12px;
  }

  .citio-ma .confirm-btn {
      flex: 1;
      padding: 8px 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
  }

  .citio-ma .confirm-btn.success { background: #28a745; color: #fff }
  .citio-ma .confirm-btn.warning { background: #ffc107; color: #000 }
  .citio-ma .confirm-btn.danger { background: #dc3545; color: #fff }

  /* ===== Interactive Buttons ===== */
  .citio-ma .interactive-buttons {
      display: flex;
      gap: 8px;
      margin-top: 12px;
      flex-wrap: wrap;
  }

  .citio-ma .interactive-btn {
      padding: 10px 16px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.2s;
      font-weight: 500;
      flex: 1;
      min-width: fit-content;
  }

  .citio-ma .interactive-btn.confirm {
      background: #28a745;
      color: white;
  }

  .citio-ma .interactive-btn.edit {
      background: #ffc107;
      color: #212529;
  }

  .citio-ma .interactive-btn.cancel {
      background: #dc3545;
      color: white;
  }

  .citio-ma .interactive-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }

  /* ===== Chat Input Area ===== */
  .citio-ma .chat-input-area {
      background: #fff;
      border-top: 1px solid #e5e7eb;
      padding: 16px;
      display: flex;
      align-items: flex-end;
      gap: 12px;
      flex-shrink: 0;
  }

  .citio-ma .input-container {
      flex: 1;
      position: relative;
  }

  .citio-ma .chat-input {
      width: 100%;
      border: 2px solid #e5e7eb;
      border-radius: 20px;
      padding: 12px 16px;
      font-size: 14px;
      resize: none;
      max-height: 100px;
      background: #f8f9fa;
      transition: all 0.2s;
      box-sizing: border-box;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      background-image: none !important;
  }

  .citio-ma .chat-input::-webkit-outer-spin-button,
  .citio-ma .chat-input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
  }

  .citio-ma .chat-input::-webkit-search-decoration,
  .citio-ma .chat-input::-webkit-search-cancel-button,
  .citio-ma .chat-input::-webkit-search-results-button,
  .citio-ma .chat-input::-webkit-search-results-decoration {
      display: none;
  }

  .citio-ma .chat-input::-webkit-resizer {
      display: none;
  }

  .citio-ma .chat-input:focus {
      outline: none;
      border-color: #128c7e;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(18, 140, 126, 0.1);
  }

  .citio-ma .send-btn {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: #128c7e;
      border: none;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s;
      flex-shrink: 0;
  }

  .citio-ma .send-btn:hover {
      background: #0d6651;
      transform: scale(1.05);
  }

  .citio-ma .send-btn:active {
      transform: scale(0.95);
  }

  /* ===== Typing Indicator ===== */
  .citio-ma .typing-indicator {
      display: none;
      max-width: 60px;
      align-self: flex-start;
  }

  .citio-ma .typing-indicator.show {
      display: block;
  }

  .citio-ma .typing-dots {
      background: #fff;
      border-radius: 18px 18px 18px 4px;
      padding: 16px 20px;
      display: flex;
      gap: 4px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  .citio-ma .typing-dot {
      width: 8px;
      height: 8px;
      background: #999;
      border-radius: 50%;
      animation: typing 1.4s infinite;
  }

  .citio-ma .typing-dot:nth-child(2) { animation-delay: 0.2s; }
  .citio-ma .typing-dot:nth-child(3) { animation-delay: 0.4s; }

  @keyframes typing {
      0%, 60%, 100% { transform: translateY(0); opacity: 0.3; }
      30% { transform: translateY(-8px); opacity: 1; }
  }

  /* ===== Scrollbar Styling ===== */
  .citio-ma .chat-body::-webkit-scrollbar {
      width: 6px;
  }

  .citio-ma .chat-body::-webkit-scrollbar-track {
      background: transparent;
  }

  .citio-ma .chat-body::-webkit-scrollbar-thumb {
      background: rgba(0,0,0,0.2);
      border-radius: 3px;
  }

  .citio-ma .chat-body::-webkit-scrollbar-thumb:hover {
      background: rgba(0,0,0,0.3);
  }

  /* ===== Responsive Design ===== */
  @media (max-width: 480px) {
      .citio-ma .modal-dialog {
          margin: 10px;
          max-width: calc(100vw - 20px);
          height: calc(100vh - 20px);
          max-height: 450px;
      }

      .floating-assistant {
          bottom: 15px;
          right: 15px;
      }

      .assistant-btn {
          width: 55px;
          height: 55px;
          font-size: 22px;
      }

      .citio-ma .bot-message,
      .citio-ma .user-message {
          max-width: 90%;
      }

      .citio-ma .chat-title {
          max-width: 150px;
      }
  }

  /* ===== Welcome Animation ===== */
  .welcome-message {
      text-align: center;
      padding: 40px 20px;
      opacity: 0.7;
  }

  .welcome-icon {
      font-size: 48px;
      color: #128c7e;
      margin-bottom: 16px;
  }

  /* ===== REMOVED PROBLEMATIC HIDING RULES ===== */
  /* The following rules have been removed as they were interfering with other modals and buttons:
     - body::after, body::before hiding rules
     - .floating-help, .help-button hiding rules
     - .message-button, .bottom-message-btn hiding rules
     - .fixed-bottom hiding rules
     - Global pointer-events: none rules
  */
</style>

<!-- Mobile Assistant Modal -->
<div class="citio-ma">
  <div class="modal fade" id="mobileAssistantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="phone-container">
          <div class="chat-header">
            <div class="chat-header-info">
              <div class="chat-avatar">C</div>
              <div class="chat-header-text">
                <div class="chat-title">CITIO services silversky</div>
                <div class="chat-status">{{ __('Online • citio bot') }}</div>
              </div>
            </div>
            <button class="close-btn" data-bs-dismiss="modal">
              <i class="ti ti-x"></i>
            </button>
          </div>

          <div class="chat-body" id="chatBody">
            <div class="message bot-message">
              <div class="message-content">
                <div>{{ __('Send Hii to begin.') }}</div>
              </div>
              <div class="message-time">12:45</div>
            </div>
          </div>

          <div class="chat-input-area">
            <div class="input-container">
              <textarea class="chat-input" id="maInput" rows="1" placeholder="{{ __('Message') }}"></textarea>
            </div>
            <button class="send-btn" id="maSend">
              <i class="ti ti-send"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Floating Assistant Button -->
{{-- <div class="floating-assistant">
  <button class="assistant-btn" id="mobileAssistantToggle">
    <i class="ti ti-message-circle"></i>
  </button>
</div> --}}

<script>
    window.Laravel = {
        userId: @json(auth()->check() ? auth()->id() : null),
        xApiKey: @json(config('constant.langchain.x-api-key'))
    };
</script>

{{-- Keep your existing JS file --}}
@vite('resources/js/citio-assistant.js')