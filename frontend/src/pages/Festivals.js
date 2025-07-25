import React, { useState, useEffect } from 'react';
import styled from 'styled-components';
import { motion } from 'framer-motion';
import { FaCalendarAlt, FaClock, FaMusic, FaSearch, FaFire } from 'react-icons/fa';
import { GiFirework, GiDrum, GiFlowers } from 'react-icons/gi';
import { MdCelebration } from 'react-icons/md';
import axios from 'axios';

const PageContainer = styled.div`
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
`;

const PageHeader = styled.div`
  text-align: center;
  margin-bottom: 3rem;
`;

const PageTitle = styled.h1`
  font-size: 3rem;
  color: #dc2626;
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

const SearchContainer = styled.div`
  display: flex;
  justify-content: center;
  margin-bottom: 3rem;
`;

const SearchBox = styled.div`
  position: relative;
  max-width: 400px;
  width: 100%;
`;

const SearchInput = styled.input`
  width: 100%;
  padding: 1rem 1rem 1rem 3rem;
  border: 2px solid #e2e8f0;
  border-radius: 25px;
  font-size: 1rem;
  outline: none;
  transition: all 0.3s ease;

  &:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
  }
`;

const SearchIcon = styled(FaSearch)`
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
`;

const FestivalsGrid = styled.div`
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
`;

const FestivalCard = styled(motion.div)`
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(220, 38, 38, 0.1);
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
    background: ${props => props.gradient || 'linear-gradient(135deg, #dc2626, #ef4444)'};
  }
`;

const FestivalHeader = styled.div`
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 1.5rem;
`;

const FestivalName = styled.h3`
  font-size: 1.5rem;
  color: #dc2626;
  margin-bottom: 0.5rem;
  line-height: 1.3;
  flex: 1;
`;

const FestivalIcon = styled.div`
  font-size: 2.5rem;
  margin-left: 1rem;
`;

const TypeBadge = styled.span`
  background: ${props => props.color || 'linear-gradient(135deg, #fef3c7, #fbbf24)'};
  color: ${props => props.textColor || '#92400e'};
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.8rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
`;

const FestivalInfo = styled.div`
  display: flex;
  flex-direction: column;
  gap: 1rem;
`;

const InfoRow = styled.div`
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background: ${props => props.bgColor || 'rgba(220, 38, 38, 0.05)'};
  border-radius: 10px;
  transition: all 0.3s ease;

  &:hover {
    background: ${props => props.hoverColor || 'rgba(220, 38, 38, 0.1)'};
  }
`;

const InfoIcon = styled.div`
  color: ${props => props.color || '#dc2626'};
  font-size: 1.1rem;
  width: 20px;
  display: flex;
  justify-content: center;
`;

const InfoContent = styled.div`
  flex: 1;
`;

const InfoLabel = styled.span`
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
`;

const InfoValue = styled.div`
  color: ${props => props.color || '#dc2626'};
  font-weight: 500;
  margin-top: 0.25rem;
`;

const LoadingContainer = styled.div`
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
  font-size: 1.2rem;
  color: #64748b;
`;

const NoResults = styled.div`
  text-align: center;
  padding: 3rem;
  color: #64748b;
  font-size: 1.2rem;
`;

const CelebrationBanner = styled.div`
  background: linear-gradient(135deg, rgba(220, 38, 38, 0.1), rgba(239, 68, 68, 0.1));
  border-radius: 20px;
  padding: 2rem;
  margin-bottom: 3rem;
  text-align: center;
  position: relative;
  overflow: hidden;

  &:before {
    content: '🎊';
    position: absolute;
    top: 1rem;
    left: 2rem;
    font-size: 2rem;
    opacity: 0.3;
  }

  &:after {
    content: '🎉';
    position: absolute;
    top: 1rem;
    right: 2rem;
    font-size: 2rem;
    opacity: 0.3;
  }
`;

const BannerText = styled.h3`
  color: #dc2626;
  margin-bottom: 0.5rem;
`;

const BannerSubtext = styled.p`
  color: #64748b;
  font-size: 1rem;
`;

const Festivals = () => {
  const [festivals, setFestivals] = useState([]);
  const [filteredFestivals, setFilteredFestivals] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    const fetchFestivals = async () => {
      try {
        const response = await axios.get('/api/festivals');
        setFestivals(response.data.data);
        setFilteredFestivals(response.data.data);
        setLoading(false);
      } catch (error) {
        console.error('Error fetching festivals:', error);
        setLoading(false);
      }
    };

    fetchFestivals();
  }, []);

  useEffect(() => {
    const filtered = festivals.filter(festival =>
      festival.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      festival.type.toLowerCase().includes(searchTerm.toLowerCase()) ||
      festival.month.toLowerCase().includes(searchTerm.toLowerCase())
    );
    setFilteredFestivals(filtered);
  }, [searchTerm, festivals]);

  const getFestivalTheme = (type, name) => {
    if (type.includes('Harvest')) {
      return {
        gradient: 'linear-gradient(135deg, #f59e0b, #d97706)',
        icon: <GiFlowers />,
        iconColor: '#f59e0b',
        bgColor: 'rgba(245, 158, 11, 0.05)',
        hoverColor: 'rgba(245, 158, 11, 0.1)',
        badgeColor: 'linear-gradient(135deg, #fef3c7, #fbbf24)',
        badgeTextColor: '#92400e'
      };
    } else if (type.includes('Temple')) {
      return {
        gradient: 'linear-gradient(135deg, #7c3aed, #a855f7)',
        icon: <GiFirework />,
        iconColor: '#7c3aed',
        bgColor: 'rgba(124, 58, 237, 0.05)',
        hoverColor: 'rgba(124, 58, 237, 0.1)',
        badgeColor: 'linear-gradient(135deg, #ede9fe, #a855f7)',
        badgeTextColor: '#6b21a8'
      };
    } else if (type.includes('Dance')) {
      return {
        gradient: 'linear-gradient(135deg, #ec4899, #f472b6)',
        icon: <GiDrum />,
        iconColor: '#ec4899',
        bgColor: 'rgba(236, 72, 153, 0.05)',
        hoverColor: 'rgba(236, 72, 153, 0.1)',
        badgeColor: 'linear-gradient(135deg, #fce7f3, #f472b6)',
        badgeTextColor: '#be185d'
      };
    } else if (type.includes('Music')) {
      return {
        gradient: 'linear-gradient(135deg, #059669, #10b981)',
        icon: <FaMusic />,
        iconColor: '#059669',
        bgColor: 'rgba(5, 150, 105, 0.05)',
        hoverColor: 'rgba(5, 150, 105, 0.1)',
        badgeColor: 'linear-gradient(135deg, #d1fae5, #10b981)',
        badgeTextColor: '#065f46'
      };
    } else if (type.includes('Light')) {
      return {
        gradient: 'linear-gradient(135deg, #dc2626, #ef4444)',
        icon: <FaFire />,
        iconColor: '#dc2626',
        bgColor: 'rgba(220, 38, 38, 0.05)',
        hoverColor: 'rgba(220, 38, 38, 0.1)',
        badgeColor: 'linear-gradient(135deg, #fee2e2, #ef4444)',
        badgeTextColor: '#991b1b'
      };
    }
    
    return {
      gradient: 'linear-gradient(135deg, #dc2626, #ef4444)',
      icon: <MdCelebration />,
      iconColor: '#dc2626',
      bgColor: 'rgba(220, 38, 38, 0.05)',
      hoverColor: 'rgba(220, 38, 38, 0.1)',
      badgeColor: 'linear-gradient(135deg, #fee2e2, #ef4444)',
      badgeTextColor: '#991b1b'
    };
  };

  if (loading) {
    return (
      <PageContainer>
        <LoadingContainer>
          <motion.div
            animate={{ rotate: 360 }}
            transition={{ duration: 2, repeat: Infinity, ease: "linear" }}
          >
            <MdCelebration style={{ fontSize: '2rem', color: '#dc2626' }} />
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
            <MdCelebration />
            Festivals of Tamil Nadu
          </PageTitle>
          <PageDescription>
            Experience the vibrant tapestry of Tamil Nadu's festivals, where ancient traditions 
            come alive through colorful celebrations, music, dance, and spiritual devotion.
          </PageDescription>
        </motion.div>
      </PageHeader>

      <CelebrationBanner>
        <BannerText>🎭 A Year-Round Celebration of Culture</BannerText>
        <BannerSubtext>
          From harvest festivals to temple celebrations, Tamil Nadu's calendar is filled 
          with joyous occasions that bring communities together.
        </BannerSubtext>
      </CelebrationBanner>

      <SearchContainer>
        <SearchBox>
          <SearchIcon />
          <SearchInput
            type="text"
            placeholder="Search festivals, types, or months..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </SearchBox>
      </SearchContainer>

      {filteredFestivals.length === 0 ? (
        <NoResults>
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.5 }}
          >
            No festivals found matching your search.
          </motion.div>
        </NoResults>
      ) : (
        <FestivalsGrid>
          {filteredFestivals.map((festival, index) => {
            const theme = getFestivalTheme(festival.type, festival.name);
            
            return (
              <FestivalCard
                key={festival.id}
                gradient={theme.gradient}
                initial={{ opacity: 0, y: 50 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: index * 0.15, duration: 0.6 }}
                whileHover={{ scale: 1.02 }}
              >
                <FestivalHeader>
                  <div>
                    <FestivalName>{festival.name}</FestivalName>
                    <TypeBadge color={theme.badgeColor} textColor={theme.badgeTextColor}>
                      <MdCelebration size={10} />
                      {festival.type}
                    </TypeBadge>
                  </div>
                  <FestivalIcon style={{ color: theme.iconColor }}>
                    {theme.icon}
                  </FestivalIcon>
                </FestivalHeader>
                
                <FestivalInfo>
                  <InfoRow bgColor={theme.bgColor} hoverColor={theme.hoverColor}>
                    <InfoIcon color={theme.iconColor}>
                      <FaCalendarAlt />
                    </InfoIcon>
                    <InfoContent>
                      <InfoLabel>Celebrated In</InfoLabel>
                      <InfoValue color={theme.iconColor}>{festival.month}</InfoValue>
                    </InfoContent>
                  </InfoRow>
                  
                  <InfoRow bgColor={theme.bgColor} hoverColor={theme.hoverColor}>
                    <InfoIcon color={theme.iconColor}>
                      <FaClock />
                    </InfoIcon>
                    <InfoContent>
                      <InfoLabel>Duration</InfoLabel>
                      <InfoValue color={theme.iconColor}>{festival.duration}</InfoValue>
                    </InfoContent>
                  </InfoRow>
                  
                  <InfoRow bgColor={theme.bgColor} hoverColor={theme.hoverColor}>
                    <InfoIcon color={theme.iconColor}>
                      {theme.icon}
                    </InfoIcon>
                    <InfoContent>
                      <InfoLabel>Celebration Type</InfoLabel>
                      <InfoValue color={theme.iconColor}>
                        {festival.type.includes('Harvest') ? 'Community Celebration' :
                         festival.type.includes('Temple') ? 'Religious Ceremony' :
                         festival.type.includes('Dance') ? 'Cultural Performance' :
                         festival.type.includes('Music') ? 'Artistic Festival' :
                         'Traditional Celebration'}
                      </InfoValue>
                    </InfoContent>
                  </InfoRow>
                </FestivalInfo>
              </FestivalCard>
            );
          })}
        </FestivalsGrid>
      )}
    </PageContainer>
  );
};

export default Festivals;