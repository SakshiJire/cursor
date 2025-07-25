# Tamil Nadu Heritage Portal 🏛️

**வணக்கம் - Welcome to Tamil Nadu!**

A beautiful, modern web application showcasing the rich cultural heritage, districts, temples, and festivals of Tamil Nadu. Built with React frontend and Flask backend with a stunning blue-white theme.

## 🌟 Features

- **🏛️ Districts Explorer**: Discover all districts of Tamil Nadu with population and area details
- **🕉️ Sacred Temples**: Explore ancient temples with their history and architectural details  
- **🎭 Vibrant Festivals**: Experience the colorful festivals throughout the year
- **📊 Interactive Statistics**: Get insights into Tamil Nadu's demographics and culture
- **🔍 Smart Search**: Search across districts, temples, and festivals
- **📱 Responsive Design**: Beautiful UI that works on all devices
- **🎨 Blue-White Theme**: Elegant color scheme with smooth animations

## 🚀 Quick Start

### Prerequisites
- Python 3.7+ 
- Node.js 14+
- npm or yarn

### Method 1: Using Startup Scripts (Recommended)

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd tamil-nadu-heritage
   ```

2. **Start Backend Server**
   ```bash
   chmod +x start-backend.sh
   ./start-backend.sh
   ```
   The backend will start on http://localhost:5000

3. **Start Frontend (in a new terminal)**
   ```bash
   chmod +x start-frontend.sh
   ./start-frontend.sh
   ```
   The frontend will start on http://localhost:3000

### Method 2: Manual Setup

#### Backend Setup
```bash
cd backend
python3 -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
pip install -r requirements.txt
python app.py
```

#### Frontend Setup
```bash
cd frontend
npm install
npm start
```

## 🏗️ Project Structure

```
tamil-nadu-heritage/
├── backend/
│   ├── app.py              # Flask application
│   ├── requirements.txt    # Python dependencies
│   └── venv/              # Virtual environment
├── frontend/
│   ├── src/
│   │   ├── components/    # React components
│   │   │   └── Header.js  # Navigation header
│   │   ├── pages/         # Page components
│   │   │   ├── Home.js    # Landing page
│   │   │   ├── Districts.js
│   │   │   ├── Temples.js
│   │   │   ├── Festivals.js
│   │   │   └── About.js
│   │   ├── App.js         # Main app component
│   │   └── index.js       # Entry point
│   ├── public/
│   │   └── index.html     # HTML template
│   └── package.json       # Dependencies
├── start-backend.sh       # Backend startup script
├── start-frontend.sh      # Frontend startup script
└── README.md
```

## 🎨 Design Features

### Color Palette
- **Primary Blue**: #1e3a8a (Tamil Nadu's official blue)
- **Light Blue**: #3b82f6
- **White**: #ffffff
- **Purple Accents**: #7c3aed (for temples)
- **Red Accents**: #dc2626 (for festivals)
- **Green Accents**: #059669 (for general info)

### Typography
- Modern sans-serif fonts
- Tamil text support with Noto Sans Tamil
- Gradient text effects for headings

### Components
- **Responsive Cards**: Beautiful cards with hover effects
- **Smooth Animations**: Framer Motion powered transitions
- **Interactive Elements**: Hover states and micro-interactions
- **Loading States**: Elegant loading animations

## 🔧 API Endpoints

### Backend APIs
- `GET /` - Welcome message and endpoints list
- `GET /api/districts` - All districts data
- `GET /api/districts/<id>` - Specific district
- `GET /api/temples` - All temples data  
- `GET /api/temples/<id>` - Specific temple
- `GET /api/festivals` - All festivals data
- `GET /api/festivals/<id>` - Specific festival
- `GET /api/stats` - Overall statistics
- `GET /api/search?q=<query>` - Search across all data

## 🌐 Technologies Used

### Frontend
- **React 18** - Modern React with hooks
- **React Router 6** - Client-side routing
- **Styled Components** - CSS-in-JS styling
- **Framer Motion** - Smooth animations
- **Axios** - HTTP client
- **React Icons** - Beautiful icon library

### Backend  
- **Flask** - Lightweight Python web framework
- **Flask-CORS** - Cross-origin resource sharing
- **Python 3** - Modern Python features

## 📱 Responsive Design

The application is fully responsive and works beautifully on:
- 🖥️ Desktop (1200px+)
- 💻 Laptop (768px - 1199px) 
- 📱 Tablet (481px - 767px)
- 📱 Mobile (320px - 480px)

## 🎯 Features in Detail

### Home Page
- Hero section with Tamil greeting
- Feature cards with smooth animations
- Live statistics from backend
- Tamil quote with English translation

### Districts Page
- Grid layout of all districts
- Search functionality
- Population and area statistics
- Interactive cards with hover effects

### Temples Page  
- Beautiful temple cards with deity information
- Architectural details and historical context
- Color-coded by temple type
- Search by name, location, or deity

### Festivals Page
- Colorful festival cards with dynamic themes
- Different colors for different festival types
- Month-wise celebration details
- Cultural significance information

### About Page
- Comprehensive information about Tamil Nadu
- Interactive statistics
- Historical and cultural context
- Project information

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🙏 Acknowledgments

- Tamil Nadu Government for cultural information
- React and Flask communities
- All contributors and maintainers

---

**தமிழ்நாடு வாழ்க! (Long Live Tamil Nadu!)**

Made with ❤️ for Tamil Nadu's rich heritage
