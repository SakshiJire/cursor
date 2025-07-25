import React, { useState, useEffect } from 'react';
import styled from 'styled-components';
import { motion } from 'framer-motion';
import { FaInfo, FaGlobe, FaHistory, FaUsers, FaIndustry, FaGraduationCap } from 'react-icons/fa';
import { GiTigerHead, GiWheat, GiFactory } from 'react-icons/gi';
import { MdLanguage } from 'react-icons/md';
import axios from 'axios';

const PageContainer = styled.div`
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
`;

const PageHeader = styled.div`
  text-align: center;
  margin-bottom: 4rem;
`;

const PageTitle = styled.h1`
  font-size: 3rem;
  color: #059669;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
`;

const PageDescription = styled.p`
  font-size: 1.2rem;
  color: #64748b;
  max-width: 700px;
  margin: 0 auto;
  line-height: 1.6;
`;

const StatsSection = styled.section`
  background: linear-gradient(135deg, rgba(5, 150, 105, 0.1), rgba(16, 185, 129, 0.1));
  border-radius: 20px;
  padding: 3rem;
  margin: 3rem 0;
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
  border: 1px solid rgba(5, 150, 105, 0.1);
`;

const StatNumber = styled.div`
  font-size: 2.5rem;
  font-weight: bold;
  color: #059669;
  margin-bottom: 0.5rem;
`;

const StatLabel = styled.div`
  color: #64748b;
  font-weight: 500;
`;

const InfoGrid = styled.div`
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
  margin: 4rem 0;
`;

const InfoCard = styled(motion.div)`
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(5, 150, 105, 0.1);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;

  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  }

  &:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #059669, #10b981);
  }
`;

const InfoHeader = styled.div`
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
`;

const InfoIcon = styled.div`
  font-size: 2rem;
  color: #059669;
`;

const InfoTitle = styled.h3`
  font-size: 1.5rem;
  color: #059669;
  margin: 0;
`;

const InfoContent = styled.div`
  color: #64748b;
  line-height: 1.6;
`;

const HighlightBox = styled.div`
  background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(251, 191, 36, 0.1));
  border-radius: 15px;
  padding: 2rem;
  margin: 2rem 0;
  border-left: 4px solid #f59e0b;
`;

const HighlightTitle = styled.h3`
  color: #f59e0b;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
`;

const HighlightContent = styled.p`
  color: #64748b;
  line-height: 1.6;
  margin: 0;
`;

const TamilQuote = styled.div`
  background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(168, 85, 247, 0.1));
  border-radius: 20px;
  padding: 3rem;
  margin: 4rem 0;
  text-align: center;
`;

const TamilText = styled.h2`
  font-size: 2rem;
  color: #7c3aed;
  margin-bottom: 1rem;
  font-family: 'Tamil', serif;
`;

const EnglishTranslation = styled.p`
  font-size: 1.2rem;
  color: #64748b;
  font-style: italic;
  margin-bottom: 1rem;
`;

const QuoteSource = styled.cite`
  color: #7c3aed;
  font-weight: 600;
`;

const LoadingContainer = styled.div`
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
  font-size: 1.2rem;
  color: #64748b;
`;

const About = () => {
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const response = await axios.get('/api/stats');
        setStats(response.data.data);
        setLoading(false);
      } catch (error) {
        console.error('Error fetching stats:', error);
        setLoading(false);
      }
    };

    fetchStats();
  }, []);

  const infoSections = [
    {
      icon: <FaHistory />,
      title: "Rich History",
      content: "Tamil Nadu has a recorded history spanning over 2000 years. The state has been ruled by various dynasties including the Cholas, Pandyas, Pallavas, and Cheras, each contributing to its rich cultural heritage and architectural marvels."
    },
    {
      icon: <MdLanguage />,
      title: "Tamil Language",
      content: "Tamil is one of the world's oldest languages and the official language of Tamil Nadu. It has a literary tradition spanning over two millennia and is spoken by over 75 million people worldwide."
    },
    {
      icon: <FaIndustry />,
      title: "Economic Powerhouse",
      content: "Tamil Nadu is India's second-largest economy by GDP. It's a major hub for automobile manufacturing, textiles, leather, chemicals, and information technology industries."
    },
    {
      icon: <FaGraduationCap />,
      title: "Educational Hub",
      content: "Home to prestigious institutions like IIT Madras, Anna University, and numerous medical colleges. The state has a literacy rate of 80.1% and is known for its emphasis on education."
    },
    {
      icon: <GiWheat />,
      title: "Agriculture",
      content: "Tamil Nadu is a major producer of rice, sugarcane, cotton, and various spices. The state's agriculture is supported by an extensive irrigation system including the ancient Grand Anicut."
    },
    {
      icon: <GiTigerHead />,
      title: "Biodiversity",
      content: "The state hosts diverse ecosystems from the Western Ghats to coastal regions. It's home to several national parks and wildlife sanctuaries, including the famous Mudumalai and Guindy National Parks."
    }
  ];

  if (loading) {
    return (
      <PageContainer>
        <LoadingContainer>
          <motion.div
            animate={{ rotate: 360 }}
            transition={{ duration: 2, repeat: Infinity, ease: "linear" }}
          >
            <FaInfo style={{ fontSize: '2rem', color: '#059669' }} />
          </motion.div>
        </LoadingContainer>
      </PageContainer>
    );
  }

  return (
    <PageContainer>
      <PageHeader>
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8 }}
        >
          <PageTitle>
            <FaGlobe />
            About Tamil Nadu
          </PageTitle>
          <PageDescription>
            தமிழ்நாடு - "The Land of Tamils" is a state in southern India known for its 
            ancient Dravidian culture, magnificent temples, classical arts, and vibrant traditions.
          </PageDescription>
        </motion.div>
      </PageHeader>

      {stats && (
        <StatsSection>
          <h2 style={{ textAlign: 'center', marginBottom: '2rem', color: '#059669' }}>
            Tamil Nadu in Numbers
          </h2>
          <StatsGrid>
            <StatItem
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 0.2, duration: 0.6 }}
            >
              <StatNumber>{stats.total_districts}</StatNumber>
              <StatLabel>Districts</StatLabel>
            </StatItem>
            <StatItem
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 0.4, duration: 0.6 }}
            >
              <StatNumber>130,060</StatNumber>
              <StatLabel>Area (sq km)</StatLabel>
            </StatItem>
            <StatItem
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 0.6, duration: 0.6 }}
            >
              <StatNumber>7.2 Cr</StatNumber>
              <StatLabel>Population</StatLabel>
            </StatItem>
            <StatItem
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 0.8, duration: 0.6 }}
            >
              <StatNumber>80.1%</StatNumber>
              <StatLabel>Literacy Rate</StatLabel>
            </StatItem>
          </StatsGrid>
        </StatsSection>
      )}

      <HighlightBox>
        <HighlightTitle>
          <GiTigerHead />
          Did You Know?
        </HighlightTitle>
        <HighlightContent>
          Tamil Nadu is home to the world's largest ancient irrigation system - the Grand Anicut, 
          built in the 2nd century CE by Karikala Chola. This engineering marvel is still functional today!
        </HighlightContent>
      </HighlightBox>

      <InfoGrid>
        {infoSections.map((section, index) => (
          <InfoCard
            key={index}
            initial={{ opacity: 0, y: 50 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 1.0 + index * 0.15, duration: 0.6 }}
            whileHover={{ scale: 1.02 }}
          >
            <InfoHeader>
              <InfoIcon>{section.icon}</InfoIcon>
              <InfoTitle>{section.title}</InfoTitle>
            </InfoHeader>
            <InfoContent>{section.content}</InfoContent>
          </InfoCard>
        ))}
      </InfoGrid>

      <TamilQuote>
        <TamilText>யாதும் ஊரே யாவரும் கேளிர்</TamilText>
        <EnglishTranslation>
          "Every place is our home and all people are our kinfolk"
        </EnglishTranslation>
        <QuoteSource>- Kaniyan Poongundran (Purananuru)</QuoteSource>
      </TamilQuote>

      <HighlightBox>
        <HighlightTitle>
          <FaInfo />
          About This Project
        </HighlightTitle>
        <HighlightContent>
          This application showcases the rich cultural heritage, administrative divisions, 
          sacred temples, and vibrant festivals of Tamil Nadu. Built with React and Flask, 
          it provides an interactive way to explore the diversity and beauty of this incredible state.
        </HighlightContent>
      </HighlightBox>
    </PageContainer>
  );
};

export default About;