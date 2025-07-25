import React from 'react';
import { 
  Typography, 
  Grid, 
  Card, 
  CardContent, 
  Box,
  List,
  ListItem,
  ListItemText,
  Chip
} from '@mui/material';
import { 
  School, 
  People, 
  AttachMoney, 
  Assignment, 
  EventNote,
  BarChart
} from '@mui/icons-material';
import { useAuth } from '../../contexts/AuthContext';

const Dashboard: React.FC = () => {
  const { user } = useAuth();

  const getDashboardContent = () => {
    switch (user?.user_type) {
      case 'admin':
        return (
          <Grid container spacing={3}>
            <Grid item xs={12} md={6} lg={3}>
              <Card>
                <CardContent>
                  <Box display="flex" alignItems="center">
                    <People color="primary" sx={{ mr: 2 }} />
                    <Box>
                      <Typography color="textSecondary" gutterBottom>
                        Total Students
                      </Typography>
                      <Typography variant="h5">
                        150
                      </Typography>
                    </Box>
                  </Box>
                </CardContent>
              </Card>
            </Grid>
            <Grid item xs={12} md={6} lg={3}>
              <Card>
                <CardContent>
                  <Box display="flex" alignItems="center">
                    <School color="primary" sx={{ mr: 2 }} />
                    <Box>
                      <Typography color="textSecondary" gutterBottom>
                        Total Staff
                      </Typography>
                      <Typography variant="h5">
                        25
                      </Typography>
                    </Box>
                  </Box>
                </CardContent>
              </Card>
            </Grid>
            <Grid item xs={12} md={6} lg={3}>
              <Card>
                <CardContent>
                  <Box display="flex" alignItems="center">
                    <AttachMoney color="primary" sx={{ mr: 2 }} />
                    <Box>
                      <Typography color="textSecondary" gutterBottom>
                        Monthly Revenue
                      </Typography>
                      <Typography variant="h5">
                        ₹2.5L
                      </Typography>
                    </Box>
                  </Box>
                </CardContent>
              </Card>
            </Grid>
            <Grid item xs={12} md={6} lg={3}>
              <Card>
                <CardContent>
                  <Box display="flex" alignItems="center">
                    <Assignment color="primary" sx={{ mr: 2 }} />
                    <Box>
                      <Typography color="textSecondary" gutterBottom>
                        Pending Tasks
                      </Typography>
                      <Typography variant="h5">
                        8
                      </Typography>
                    </Box>
                  </Box>
                </CardContent>
              </Card>
            </Grid>
          </Grid>
        );

      case 'teacher':
        return (
          <Grid container spacing={3}>
            <Grid item xs={12} md={6}>
              <Card>
                <CardContent>
                  <Typography variant="h6" gutterBottom>
                    My Classes
                  </Typography>
                  <List>
                    <ListItem>
                      <ListItemText 
                        primary="5th Grade - Mathematics" 
                        secondary="40 Students"
                      />
                    </ListItem>
                    <ListItem>
                      <ListItemText 
                        primary="10th Grade - Advanced Math" 
                        secondary="35 Students"
                      />
                    </ListItem>
                  </List>
                </CardContent>
              </Card>
            </Grid>
            <Grid item xs={12} md={6}>
              <Card>
                <CardContent>
                  <Typography variant="h6" gutterBottom>
                    Today's Schedule
                  </Typography>
                  <List>
                    <ListItem>
                      <ListItemText 
                        primary="9:00 AM - 10:00 AM" 
                        secondary="5th Grade Mathematics"
                      />
                    </ListItem>
                    <ListItem>
                      <ListItemText 
                        primary="11:00 AM - 12:00 PM" 
                        secondary="10th Grade Mathematics"
                      />
                    </ListItem>
                  </List>
                </CardContent>
              </Card>
            </Grid>
          </Grid>
        );

      case 'student':
        return (
          <Grid container spacing={3}>
            <Grid item xs={12} md={6}>
              <Card>
                <CardContent>
                  <Typography variant="h6" gutterBottom>
                    Academic Information
                  </Typography>
                  <Typography>Class: {user.student?.class?.name}</Typography>
                  <Typography>Roll Number: {user.student?.roll_number}</Typography>
                  <Typography>Admission Number: {user.student?.admission_number}</Typography>
                </CardContent>
              </Card>
            </Grid>
            <Grid item xs={12} md={6}>
              <Card>
                <CardContent>
                  <Typography variant="h6" gutterBottom>
                    Recent Assignments
                  </Typography>
                  <List>
                    <ListItem>
                      <ListItemText 
                        primary="Mathematics Assignment" 
                        secondary="Due: Tomorrow"
                      />
                      <Chip label="Pending" color="warning" size="small" />
                    </ListItem>
                    <ListItem>
                      <ListItemText 
                        primary="Science Project" 
                        secondary="Due: Next Week"
                      />
                      <Chip label="In Progress" color="info" size="small" />
                    </ListItem>
                  </List>
                </CardContent>
              </Card>
            </Grid>
          </Grid>
        );

      case 'parent':
        return (
          <Grid container spacing={3}>
            <Grid item xs={12}>
              <Card>
                <CardContent>
                  <Typography variant="h6" gutterBottom>
                    My Children
                  </Typography>
                  {user.children?.map((child, index) => (
                    <Box key={index} sx={{ mb: 2, p: 2, border: '1px solid #e0e0e0', borderRadius: 1 }}>
                      <Typography variant="subtitle1">
                        {child.user?.first_name} {child.user?.last_name}
                      </Typography>
                      <Typography variant="body2" color="textSecondary">
                        Class: {child.class?.name} | Roll No: {child.roll_number}
                      </Typography>
                      <Typography variant="body2" color="textSecondary">
                        Admission No: {child.admission_number}
                      </Typography>
                    </Box>
                  ))}
                </CardContent>
              </Card>
            </Grid>
          </Grid>
        );

      default:
        return (
          <Typography variant="h6">
            Welcome to the ERP System
          </Typography>
        );
    }
  };

  return (
    <Box>
      <Typography variant="h4" gutterBottom>
        Dashboard
      </Typography>
      <Typography variant="subtitle1" color="textSecondary" gutterBottom>
        Welcome back, {user?.first_name}!
      </Typography>
      <Box sx={{ mt: 3 }}>
        {getDashboardContent()}
      </Box>
    </Box>
  );
};

export default Dashboard;