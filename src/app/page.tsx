import React from 'react';
import Hero from '@/components/sections/Hero';
import Countdown from '@/components/sections/Countdown';
import About from '@/components/sections/About';
import Categories from '@/components/sections/Categories';
import Prizes from '@/components/sections/Prizes';
import Schedule from '@/components/sections/Schedule';
import Rules from '@/components/sections/Rules';
import Contact from '@/components/sections/Contact';

export default function Home() {
  return (
    <>
      <Hero />
      <Countdown />
      <About />
      <Categories />
      <Prizes />
      <Schedule />
      <Rules />
      <Contact />
    </>
  );
}
