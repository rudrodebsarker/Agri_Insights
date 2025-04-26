const languages = {
    ENGLISH: null,
    BENGALI: null
  };

  let chatbotData = null;

  // fetching static data, replace with api call when backend complete 
  fetch('data/chatbotData.json')
  .then(response => response.json())
  .then(data => {
    languages.ENGLISH = data.en;
    languages.BENGALI = data.bn;
    chatbotData = languages.ENGLISH;
  })
  .catch(error => {
    console.error('Error loading chatbot data:', error);
  });
  
  const chatWindow = document.getElementById('chatWindow');
  const openChat = document.getElementById('openChat');
  const closeChat = document.getElementById('closeChat');
  const chatMessages = document.getElementById('chatMessages');
  const optionsContainer = document.getElementById('optionsContainer');
  const mainMenu = document.getElementById('mainMenu');

  // hide chat window
  openChat.addEventListener('click', () => {
    chatWindow.classList.remove('hidden');
    openChat.classList.add('hidden');
    startChat();
    startLanguageSelection()
  });
  
  // show chat window
  closeChat.addEventListener('click', () => {
    chatWindow.classList.add('hidden');
    openChat.classList.remove('hidden');
    resetChat();
  });

  // Language selection
  function startLanguageSelection(){
    addBotMessage("🌐 Please choose your language / আপনার ভাষা নির্বাচন করুন:");
    renderLanguageOptions();
  }

  // show laguage options
  function renderLanguageOptions() {
    optionsContainer.innerHTML = '';
    const languagesOptions = [
      { label: "🇬🇧 English", value: "ENGLISH" },
      { label: "🇧🇩 বাংলা", value: "BENGALI" }
    ];
    languagesOptions.forEach(lang => {
      const button = document.createElement('button');
      button.className = "bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm";
      button.innerText = lang.label;
      button.addEventListener('click', () => selectLanguage(lang.value));
      optionsContainer.appendChild(button);
    });
  }
  
  function selectLanguage(selectedLang) {
    chatbotData = languages[selectedLang];
    resetChat(); 
    startChat(); 
  }
  
  // start
  function startChat() {
    addBotMessage(chatbotData.start.message);
    renderOptions(chatbotData.start.options);
    renderMainMenu();
  }
  
  function resetChat() {
    chatMessages.innerHTML = '';
    optionsContainer.innerHTML = '';
  }
  
  function addBotMessage(text) {
    const message = document.createElement('div');
    message.className = "flex justify-start";
    message.innerHTML = `<div class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg max-w-xs">${text}</div>`;
    chatMessages.appendChild(message);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }
  
  function addUserMessage(text) {
    const message = document.createElement('div');
    message.className = "flex justify-end";
    message.innerHTML = `<div class="bg-blue-100 text-blue-700 px-4 py-2 rounded-lg max-w-xs">${text}</div>`;
    chatMessages.appendChild(message);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }
  
  function renderOptions(options) {
    optionsContainer.innerHTML = '';
    if (!options) return;
  
    options.forEach(option => {
      const button = document.createElement('button');
      button.className = "bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm hover:bg-blue-200 transition";
      button.textContent = getEmoji(option) + " " + option;
      button.onclick = () => sendOption(option);
      optionsContainer.appendChild(button);
    });
  }
  
  function renderMainMenu() {
    mainMenu.innerHTML = '';
    chatbotData.start.options.forEach(option => {
      const button = document.createElement('button');
      button.className = "bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs hover:bg-blue-100 transition";
    //   button.className = "bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm";
      button.textContent = getEmoji(option) + " " + option;
      button.onclick = () => sendOption(option);
      mainMenu.appendChild(button);
    });
  }
  
  function sendOption(option) {
    addUserMessage(option);
  
    const response = chatbotData[option];
    if (response) {
      addBotMessage(response.message);
      renderOptions(response.options);
    } else {
      optionsContainer.innerHTML = '';
    }
  }

// Add the other emojis for other options, this mapping is incomplete.
function getEmoji(option) {
  const emojiMap = {
    // English options
    "Crop Suggestion": "🌾",
    "Pest Control": "🐛",
    "Weather Info": "☀️",
    "Personalized Advice": "🧑‍🌾",
    "Winter": "❄️",
    "Summer": "☀️",
    "Rainy": "🌧️",

    // Bengali options
    "ফসল পরামর্শ": "🌾",
    "পোকা নিয়ন্ত্রণ": "🐛",
    "আবহাওয়া তথ্য": "☀️",
    "ব্যক্তিগত পরামর্শ": "🧑‍🌾",
    "শীতকাল": "❄️",
    "গ্রীষ্মকাল": "☀️",
    "বর্ষাকাল": "🌧️",
    "ব্রাউন প্ল্যান্টহপার": "🪲",
    "স্টেম বোরার": "🪱",
    "ফল ও শাখা বোরার": "🦋",
    "অ্যাফিডস": "🐞"
  };
  return emojiMap[option] || "👉";
}