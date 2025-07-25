from flask import Flask, jsonify, request
from flask_cors import CORS
import json
import os
from datetime import datetime

app = Flask(__name__)
CORS(app)

# Sample data for Tamil Nadu related content
districts = [
    {"id": 1, "name": "Chennai", "population": "4646732", "area": "426 sq km"},
    {"id": 2, "name": "Coimbatore", "population": "1061447", "area": "642 sq km"},
    {"id": 3, "name": "Madurai", "population": "1017865", "area": "518 sq km"},
    {"id": 4, "name": "Tiruchirappalli", "population": "916857", "area": "167 sq km"},
    {"id": 5, "name": "Salem", "population": "831038", "area": "125 sq km"},
    {"id": 6, "name": "Tirunelveli", "population": "474838", "area": "169 sq km"},
    {"id": 7, "name": "Vellore", "population": "423425", "area": "87 sq km"},
    {"id": 8, "name": "Erode", "population": "498129", "area": "112 sq km"},
    {"id": 9, "name": "Thanjavur", "population": "290720", "area": "128 sq km"},
    {"id": 10, "name": "Kanchipuram", "population": "235810", "area": "112 sq km"}
]

temples = [
    {"id": 1, "name": "Meenakshi Amman Temple", "location": "Madurai", "deity": "Meenakshi", "built": "6th century CE"},
    {"id": 2, "name": "Brihadeeswarar Temple", "location": "Thanjavur", "deity": "Shiva", "built": "1010 CE"},
    {"id": 3, "name": "Kapaleeshwarar Temple", "location": "Chennai", "deity": "Shiva", "built": "7th century CE"},
    {"id": 4, "name": "Ramanathaswamy Temple", "location": "Rameswaram", "deity": "Shiva", "built": "12th century CE"},
    {"id": 5, "name": "Thiruvannamalai Temple", "location": "Thiruvannamalai", "deity": "Shiva", "built": "9th century CE"}
]

festivals = [
    {"id": 1, "name": "Pongal", "month": "January", "type": "Harvest Festival", "duration": "4 days"},
    {"id": 2, "name": "Meenakshi Thirukalyanam", "month": "April-May", "type": "Temple Festival", "duration": "10 days"},
    {"id": 3, "name": "Natyanjali", "month": "February-March", "type": "Dance Festival", "duration": "5 days"},
    {"id": 4, "name": "Chennai Music Season", "month": "December-January", "type": "Music Festival", "duration": "60 days"},
    {"id": 5, "name": "Karthigai Deepam", "month": "November-December", "type": "Light Festival", "duration": "10 days"}
]

@app.route('/')
def home():
    return jsonify({
        "message": "Welcome to Tamil Nadu API",
        "description": "Explore the rich culture and heritage of Tamil Nadu",
        "endpoints": [
            "/api/districts",
            "/api/temples", 
            "/api/festivals",
            "/api/stats"
        ]
    })

@app.route('/api/districts')
def get_districts():
    return jsonify({
        "success": True,
        "data": districts,
        "count": len(districts)
    })

@app.route('/api/districts/<int:district_id>')
def get_district(district_id):
    district = next((d for d in districts if d["id"] == district_id), None)
    if district:
        return jsonify({
            "success": True,
            "data": district
        })
    return jsonify({
        "success": False,
        "message": "District not found"
    }), 404

@app.route('/api/temples')
def get_temples():
    return jsonify({
        "success": True,
        "data": temples,
        "count": len(temples)
    })

@app.route('/api/temples/<int:temple_id>')
def get_temple(temple_id):
    temple = next((t for t in temples if t["id"] == temple_id), None)
    if temple:
        return jsonify({
            "success": True,
            "data": temple
        })
    return jsonify({
        "success": False,
        "message": "Temple not found"
    }), 404

@app.route('/api/festivals')
def get_festivals():
    return jsonify({
        "success": True,
        "data": festivals,
        "count": len(festivals)
    })

@app.route('/api/festivals/<int:festival_id>')
def get_festival(festival_id):
    festival = next((f for f in festivals if f["id"] == festival_id), None)
    if festival:
        return jsonify({
            "success": True,
            "data": festival
        })
    return jsonify({
        "success": False,
        "message": "Festival not found"
    }), 404

@app.route('/api/stats')
def get_stats():
    total_population = sum(int(d["population"]) for d in districts)
    total_area = sum(float(d["area"].split()[0]) for d in districts)
    
    return jsonify({
        "success": True,
        "data": {
            "total_districts": len(districts),
            "total_temples": len(temples),
            "total_festivals": len(festivals),
            "total_population": total_population,
            "total_area": f"{total_area:.1f} sq km",
            "last_updated": datetime.now().isoformat()
        }
    })

@app.route('/api/search')
def search():
    query = request.args.get('q', '').lower()
    if not query:
        return jsonify({
            "success": False,
            "message": "Search query is required"
        }), 400
    
    results = {
        "districts": [d for d in districts if query in d["name"].lower()],
        "temples": [t for t in temples if query in t["name"].lower() or query in t["location"].lower()],
        "festivals": [f for f in festivals if query in f["name"].lower() or query in f["type"].lower()]
    }
    
    return jsonify({
        "success": True,
        "query": query,
        "data": results,
        "total_results": len(results["districts"]) + len(results["temples"]) + len(results["festivals"])
    })

if __name__ == '__main__':
    app.run(debug=True, port=5000)