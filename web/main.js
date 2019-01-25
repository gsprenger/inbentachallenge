Vue.component('Chat', {
	template: `
	<div id="appMain">
		<div id="topbar">Yodabot</div>
		<div id="prevmsglist">
	    <ul>
	      <li v-for="message in historyMessages" :class="[message.fromYou ? 'fromyou' : 'frombot']">
	        <span v-if="message.fromYou">You</span><span v-else>YodaBot</span>{{message.content}}
	      </li>
	    </ul>
	  </div>
	  <div v-show="historyMessages.length>0" id="prevmsg">Previous messages</div>
    <p v-if="messages.length == 0 && historyMessages.length == 0">
      <em>Type something to start chatting with YodaBot!</em>
    </p>
		<div id="msglist">
	    <ul>
	      <li v-for="message in messages" :class="[message.fromYou ? 'fromyou' : 'frombot']">
	        <span v-if="message.fromYou">You</span><span v-else>YodaBot</span>{{message.content}}
	      </li>
	    </ul>
	    <div v-show="botIsWriting" class="iswriting">
	      YodaBot is writing...
	    </div>
    </div>
		<SendMessageForChat @newmessage="newMessage"></SendMessageForChat>
		<button @click="clearChat">Clear all history</button>
	</div>
	`,
	data() {
		return {
			historyMessages: [],
			messages: [],
      botIsWriting: false
		}
	},
  methods: {
    newMessage(message) {
    	// Message from user
    	var messageObj = {fromYou: true, content: message};
      this.messages.push(messageObj);
      this.botIsWriting = true;
      this.sendMessage(message);
      this.saveMessageToHistory(messageObj);
    },
    receiveMessage(message) {
    	// Message from bot
    	var messageObj = {fromYou: false, content: message};
      this.messages.push(messageObj);
      this.botIsWriting = false;
      this.saveMessageToHistory(messageObj);
    },
    sendMessage(message) {
    	var that = this;
    	var xhr = new XMLHttpRequest();
			xhr.addEventListener("load", function() {
    		that.receiveMessage(xhr.responseText);
			});
			xhr.open("POST", "sendMessage.php");
			xhr.setRequestHeader('Content-type', 'application/json')
			xhr.send(JSON.stringify({"message": message}));
    },
    saveMessageToHistory(message) {
    	$msgHistory = this.getHistory();
    	$msgHistory.push(message);
    	localStorage.setItem('yodabot.messages', JSON.stringify($msgHistory))
    },
    getHistory() {
    	var msgHistory = localStorage.getItem('yodabot.messages');
    	if (msgHistory) {
    		return JSON.parse(msgHistory);	
    	} else {
    		// If there is no history yet, create the entry in localStorage
    		localStorage.setItem('yodabot.messages', JSON.stringify([]));
    		return [];
    	}
    	
    },
    clearChat() {
			localStorage.setItem('yodabot.messages', JSON.stringify([]));
			this.historyMessages = [];
			this.messages = [];
    }
  },
  created: function () {
  	this.historyMessages = this.getHistory();
  },
  watch: {
  	messages: function() {
  		// Whenever a new message comes, we want to scroll to the bottom
      setTimeout(function() {
      	window.scrollTo(0,document.body.scrollHeight);
      }, 50);
  	}
  }
})

Vue.component('SendMessageForChat', {
	template: `
	<div id="sendmsg">
		<form @submit.prevent="send">
			<input type="text" v-model="newMessage" autofocus placeholder="Type something and press Enter to send" autocomplete="off">
		  <!-- <button><i class="fas fa-paper-plane"></i></button> -->
		</form>
	</div>
	`,
	data() {
		return {
			newMessage: ""
		}
	},
  methods: {
    send() {
      if (this.newMessage) {
        this.$emit('newmessage', this.newMessage);
        this.newMessage = "";
      }
    }
  }
})

var app = new Vue({
	el: '#app'
})
