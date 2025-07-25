import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import styled from 'styled-components';
import { motion } from 'framer-motion';
import { FaMapMarkedAlt, FaTemple, FaCalendarAlt, FaChartBar } from 'react-icons/fa';
import { GiTigerHead } from 'react-icons/gi';
import axios from 'axios';

const HomeContainer = styled.div`
  min-height: 100vh;
  padding: 2rem;
`;

const HeroSection = styled(motion.section)`
  text-align: center;
  padding: 4rem 0;
  max-width: 1200px;
  margin: 0 auto;
`;

const HeroTitle = styled(motion.h1)`
  font-size: 3.5rem;
  font-weight: bold;
  background: linear-gradient(135deg, #1e3a8a, #3b82f6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 1rem;

  @media (max-width: 768px) {
    font-size: 2.5rem;
  }
`;

const HeroSubtitle = styled(motion.p)`
  font-size: 1.25rem;
  color: #64748b;
  margin-bottom: 3rem;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.6;
`;

const FeaturesGrid = styled.div`
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  padding: 4rem 0;
`;

const FeatureCard = styled(motion.div)`
  background: rgba(255, 255, 255, 0.9);
  border-radius: 20px;
  padding: 2rem;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(30, 58, 138, 0.1);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;

  &:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  }

  &:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
  }

  &:hover:before {
    left: 100%;
  }
`;

const FeatureIcon = styled.div`
  font-size: 3rem;
  color: #1e3a8a;
  margin-bottom: 1rem;
  display: flex;
  justify-content: center;
`;

const FeatureTitle = styled.h3`
  font-size: 1.5rem;
  margin-bottom: 1rem;
  color: #1e40af;
`;

const FeatureDescription = styled.p`
  color: #64748b;
  line-height: 1.6;
  margin-bottom: 1.5rem;
`;

const FeatureButton = styled(Link)`
  display: inline-block;
  background: linear-gradient(135deg, #1e3a8a, #3b82f6);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  font-weight: 500;
  transition: all 0.3s ease;
  text-decoration: none;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(30, 58, 138, 0.3);
  }
`;

const StatsSection = styled.section`
  background: rgba(30, 58, 138, 0.05);
  border-radius: 20px;
  padding: 3rem;
  margin: 4rem auto;
  max-width: 1200px;
`;

const StatsGrid = styled.div`
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
  text-align: center;
`;

const StatItem = styled(motion.div)`
  padding: 1.5rem;
  background: white;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
`;

const StatNumber = styled.div`
  font-size: 2.5rem;
  font-weight: bold;
  color: #1e3a8a;
  margin-bottom: 0.5rem;
`;

const StatLabel = styled.div`
  color: #64748b;
  font-weight: 500;
`;

const QuoteSection = styled.section`
  text-align: center;
  padding: 4rem 2rem;
  background: linear-gradient(135deg, rgba(30, 58, 138, 0.1), rgba(59, 130, 246, 0.1));
  border-radius: 20px;
  margin: 4rem auto;
  max-width: 800px;
`;

const Quote = styled.blockquote`
  font-size: 1.5rem;
  font-style: italic;
  color: #1e40af;
  margin-bottom: 1rem;
  line-height: 1.6;
`;

const QuoteAuthor = styled.cite`
  color: #64748b;
  font-size: 1rem;
`;

const Home = () => {
  const [stats, setStats] = useState(null);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const response = await axios.get('/api/stats');
        setStats(response.data.data);
      } catch (error) {
        console.error('Error fetching stats:', error);
      }
    };

    fetchStats();
  }, []);

  const features = [
    {
      icon: <FaMapMarkedAlt />,
      title: "Districts",
      description: "Explore the 38 districts of Tamil Nadu, each with its unique culture, history, and significance.",
      link: "/districts",
      color: "#1e3a8a"
    },
    {
      icon: <FaTemple />,
      title: "Temples",
      description: "Discover ancient temples and architectural marvels that showcase Tamil Nadu's spiritual heritage.",
      link: "/temples",
      color: "#7c3aed"
    },
    {
      icon: <FaCalendarAlt />,
      title: "Festivals",
      description: "Experience the vibrant festivals and cultural celebrations that bring Tamil Nadu to life.",
      link: "/festivals",
      color: "#dc2626"
    },
    {
      icon: <FaChartBar />,
      title: "Statistics",
      description: "Get insights into demographics, economy, and development across Tamil Nadu.",
      link: "/about",
      color: "#059669"
    }
  ];

  return (
    <HomeContainer>
      <HeroSection
        initial={{ opacity: 0, y: 50 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.8 }}
      >
        <HeroTitle
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2, duration: 0.8 }}
        >
          வணக்கம் - Welcome to Tamil Nadu
        </HeroTitle>
        <HeroSubtitle
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.4, duration: 0.8 }}
        >
          Discover the land of temples, rich culture, and ancient traditions. 
          Tamil Nadu - where every corner tells a story of heritage and progress.
        </HeroSubtitle>
      </HeroSection>

      <FeaturesGrid>
        {features.map((feature, index) => (
          <FeatureCard
            key={index}
            initial={{ opacity: 0, y: 50 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.6 + index * 0.2, duration: 0.8 }}
            whileHover={{ scale: 1.02 }}
          >
            <FeatureIcon style={{ color: feature.color }}>
              {feature.icon}
            </FeatureIcon>
            <FeatureTitle>{feature.title}</FeatureTitle>
            <FeatureDescription>{feature.description}</FeatureDescription>
            <FeatureButton to={feature.link}>
              Explore {feature.title}
            </FeatureButton>
          </FeatureCard>
        ))}
      </FeaturesGrid>

      {stats && (
        <StatsSection>
          <h2 style={{ textAlign: 'center', marginBottom: '2rem', color: '#1e3a8a' }}>
            Tamil Nadu at a Glance
          </h2>
          <StatsGrid>
            <StatItem
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 1.4, duration: 0.6 }}
            >
              <StatNumber>{stats.total_districts}</StatNumber>
              <StatLabel>Districts</StatLabel>
            </StatItem>
            <StatItem
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 1.6, duration: 0.6 }}
            >
              <StatNumber>{stats.total_temples}</StatNumber>
              <StatLabel>Famous Temples</StatLabel>
            </StatItem>
            <StatItem
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 1.8, duration: 0.6 }}
            >
              <StatNumber>{stats.total_festivals}</StatNumber>
              <StatLabel>Major Festivals</StatLabel>
            </StatItem>
            <StatItem
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 2.0, duration: 0.6 }}
            >
              <StatNumber>{(stats.total_population / 10000000).toFixed(1)}M</StatNumber>
              <StatLabel>Population (Crores)</StatLabel>
            </StatItem>
          </StatsGrid>
        </StatsSection>
      )}

      <QuoteSection>
        <Quote>
          "Tamil Nadu is not just a state, it's a civilization that has contributed immensely to 
          India's cultural, spiritual, and intellectual heritage."
        </Quote>
        <QuoteAuthor>- Ancient Tamil Wisdom</QuoteAuthor>
      </QuoteSection>
    </HomeContainer>
  );
};

export default Home;