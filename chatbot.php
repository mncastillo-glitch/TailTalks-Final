<div class="chat-wrapper" id="chatWrapper">
    <div class="chat-header" onclick="toggleChat()">
        <div class="header-info">
            <span class="status-dot"></span>
            <strong>TailTalks Assistant 🐾</strong>
        </div>
        <i class="fas fa-chevron-down" id="chatChevron"></i>
    </div>
    
    <div class="chat-body" id="chatBody">
        <div class="msg ai">
            Hi! 👋 I'm your TailTalks companion. How can I help you or your furry friends today?
            <div class="quick-btns">
                <button onclick="quickAsk('About Us')">About Us</button>
                <button onclick="quickAsk('Dogs')">Dogs</button>
                <button onclick="quickAsk('Cats')">Cats</button>
                <button onclick="quickAsk('Health Tips')">Health Tips</button>
            </div>
        </div>
    </div>

    <div id="typing" style="display: none; padding: 10px 20px; font-size: 0.8rem; color: #5dade2; font-style: italic;">
        Assistant is typing...
    </div>

    <div class="chat-input">
        <input type="text" id="aiInput" placeholder="Ask about breeds, health, or us..." onkeypress="checkEnter(event)">
        <button onclick="sendMsg()"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<style>
    /* ... Keep your existing CSS ... */
    .chat-wrapper {
        position: fixed; bottom: 20px; right: 20px; width: 350px;
        background: rgba(11, 21, 34, 0.98); backdrop-filter: blur(15px);
        border: 1px solid rgba(255,255,255,0.1); border-radius: 20px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.8); z-index: 10000;
        display: flex; flex-direction: column; transition: 0.4s ease;
    }
    .chat-wrapper.minimized { transform: translateY(calc(100% - 60px)); }
    .chat-header { background: #5dade2; color: #0b1522; padding: 15px 20px; border-radius: 18px 18px 0 0; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
    .status-dot { height: 8px; width: 8px; background: #27ae60; border-radius: 50%; display: inline-block; margin-right: 8px; }
    .chat-body { height: 320px; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; }
    .msg { padding: 12px 16px; border-radius: 15px; font-size: 0.85rem; line-height: 1.4; max-width: 85%; }
    .ai { background: rgba(255, 255, 255, 0.05); color: white; align-self: flex-start; border-bottom-left-radius: 2px; }
    .user { background: #5dade2; color: #0b1522; align-self: flex-end; border-bottom-right-radius: 2px; font-weight: 600; }
    .quick-btns { margin-top: 10px; display: flex; gap: 5px; flex-wrap: wrap; }
    .quick-btns button { background: rgba(93, 173, 226, 0.1); border: 1px solid #5dade2; color: #5dade2; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; cursor: pointer; }
    .chat-input { padding: 15px; display: flex; gap: 10px; border-top: 1px solid rgba(255,255,255,0.1); }
    .chat-input input { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 10px; border-radius: 10px; color: white; outline: none; }
    .chat-input button { background: #5dade2; border: none; width: 40px; border-radius: 10px; cursor: pointer; color: #0b1522; }
</style>

<script>
    function toggleChat() { document.getElementById('chatWrapper').classList.toggle('minimized'); }
    function checkEnter(e) { if (e.key === 'Enter') sendMsg(); }
    function quickAsk(topic) { document.getElementById('aiInput').value = topic; sendMsg(); }

    function sendMsg() {
        const input = document.getElementById('aiInput');
        const body = document.getElementById('chatBody');
        const typing = document.getElementById('typing');
        const text = input.value.trim();
        if (!text) return;

        body.innerHTML += `<div class="msg user">${text}</div>`;
        input.value = "";
        body.scrollTop = body.scrollHeight;

        // Show typing indicator
        typing.style.display = "block";

        let reply = "";
        const lowText = text.toLowerCase();

        // 1. EXPANDED CONVERSATION & ABOUT
        if (lowText.match(/^(hi|hello|hey)/)) {
            reply = "Hello! 👋 I'm here to help you find the perfect pet companion. How's your day going?";
        } 
        else if (lowText.includes("how are you")) {
            reply = "I'm doing great! Just wagging my virtual tail. 🐶 How about you?";
        }
        else if (lowText.includes("about") || lowText.includes("who are you") || lowText.includes("info")) {
            reply = "TailTalks is a Quezon City-based pet encyclopedia! 📖 We focus on educating owners about breed characteristics and connecting pets with loving homes.";
        }
         else if (lowText.includes("thank you")) {
            reply = "You're welcome! Is there anything else I can help you with?";
        }

        // 2. DETAILED DOG INFO (Improved to catch specific breeds first)
        else if (lowText.includes("golden")) {
            reply = "Golden Retrievers are friendly and devoted! 🐕 They are known for their intelligence and make excellent family pets. They do need plenty of exercise!";
        }
        else if (lowText.includes("husky")) {
            reply = "Siberian Huskies are high-energy and vocal! ❄️ They are famous for their thick double coats and striking blue or multi-colored eyes.";
        }
        else if (lowText.includes("shih tzu")) {
            reply = "Shih Tzus are sturdy little 'lion dogs.' 🦁 They are affectionate, happy, and love to follow their owners from room to room!";
        }
        else if (lowText.includes("dog")) {
            reply = "Dogs are amazing! 🐕 We have info on Goldens, Huskies, and Shih Tzus. Which one are you interested in?";
        }

        // 3. DETAILED CAT INFO
        else if (lowText.includes("bengal")) {
            reply = "Bengals are active and curious! 🐆 They are known for their leopard-like spots and high energy—they often even like playing in water!";
        }
        else if (lowText.includes("persian")) {
            reply = "Persians are quiet and sweet. ☁️ They are the most popular breed of pedigree cats and are famous for their long, beautiful fur.";
        }
        else if (lowText.includes("siamese")) {
            reply = "Siamese cats are very talkative! 🗣️ They are sleek, social, and love to be the center of attention.";
        }
        else if (lowText.includes("cat")) {
            reply = "Cats are royalty! 🐱 Check out our guides on Bengals, Persians, and Siamese cats. Do you have a favorite?";
        }

        // 4. DETAILED BIRD & HAMSTER INFO
        else if (lowText.includes("bird") || lowText.includes("parakeet")) {
            reply = "Birds like Parakeets are highly intelligent! 🦜 They can even learn to mimic human speech and need a varied diet of seeds and fresh greens.";
        }
        else if (lowText.includes("hamster")) {
            reply = "Hamsters are nocturnal and love to run! 🐹 A Syrian hamster is a great choice for a first-time pet owner.";
        }

        // 5. HEALTH TIPS
        else if (lowText.includes("health") || lowText.includes("tip") || lowText.includes("sick")) {
            reply = "Pet Health 101: 🩺 1. Regular vet checkups are a must. 2. Keep vaccinations updated. 3. Watch for changes in eating habits. Always consult a local vet if you're worried!";
        }

        // 6. ADOPTION & HOURS
        else if (lowText.includes("adopt")) {
            reply = "Ready to adopt? 🏠 Fill out our form! We'll match you with a pet based on your lifestyle and home environment.";
        }
        else if (lowText.includes("hour") || lowText.includes("open")) {
            reply = "Our team is available Mon-Sat, 9AM-6PM. We're offline on Sundays for some pet playtime! 🕙";
        }
        
        // DEFAULT
        else {
            reply = "I'm not quite sure about that yet, but I'm an expert on Dogs, Cats, and Birds! Ask me about specific breeds (like Huskies or Bengals) or pet health tips!";
        }

        setTimeout(() => {
            typing.style.display = "none";
            body.innerHTML += `<div class="msg ai">${reply}</div>`;
            body.scrollTop = body.scrollHeight;
        }, 1200);
    }
</script>