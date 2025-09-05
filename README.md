# SNEAK-PEAK

This PHP-based chat website was something I built back in high school. I created it to communicate with my girlfriend because we weren’t allowed to access social media during school (Islamic Boarding School).
So I developed this project as a way to bypass the school’s system, allowing us to stay in touch discreetly.

# FEATURES

- Chat interface similar to social media
- Send videos, images, and voice files
- “Last seen” status tracking
- WhatsApp notifications (at the time, one of us was at home, so this helped avoid missing each other while online — this feature was removed from the repository due to data privacy concerns)

# ADDITIONAL NOTES

This project heavily relied on AI — almost entirely, in fact. Back then, my coding knowledge and skills were still limited, so I could only design the website’s flow and system.
Because of that, this website isn’t licensed under any copyright, and I’ve chosen to keep it open and unrestricted.

# SETUP

- Upload all project files to your hosting server
- In 'user_code.json', add users who are allowed to access the chat. Example:
  '''json
  {
    "sil123": "sil"
    // and so on
  }
- Tip: Improve the online status display in 'style.css' to handle more than 3 users without layout issues.
- Once configured, the website is ready to use
