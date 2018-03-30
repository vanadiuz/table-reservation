<template>
    <div id="reservation" class="trem-reservation" ref="tremReservation" v-if="view !== 4" :style="[canvasLoaded ? {height: envelopeHeight} : {}] ">
      <transition name="fade" mode="in-out">
        <div class="reservation1" ref="reservationOne" v-show="(view === 0) && (canvasLoaded === true)">
          <div class="envelope"  :style="[canvasLoaded ? {height: envelopeHeight} : ''] ">
            <h2>{{calendarTimeInitData.translation.header}}</h2>

            <div class="people-form" >
                <div class="form-element" id="dateinput">
                  <flat-pickr  :config="dateConfig" :placeholder="date" v-model="date" input-class="input"></flat-pickr>
                  <span class="trem-icon tremtr-icon-uniF10A" aria-hidden="true"></span>
                </div>
                <transition name="fade" mode="in-out">
                  <carousel v-show="(date !== '')" :navigationEnabled="true" :paginationEnabled="false" :perPage="3" :navigationNextLabel="caruselNavNext" :navigationPrevLabel="caruselNavPrev">
                    <slide v-for="(workingTime, i) of arrayOfWorkingTimes" :key="workingTime" >
                      <div  @click="selectedTime($event)" :index="i">{{workingTime}}</div>
                    </slide>
                  </carousel>
                </transition>
                <canvas id="cc" class="context-menu-one" width="1000px" height="1000px" ></canvas>
                <a class="c0ffee-button" @click="book">{{calendarTimeInitData.translation.bookTableButton}}</a>
            </div>
          </div>
        </div>
      </transition>
      <transition name="fade" mode="in-out">
        <div class="reservation2" ref="reservationTwo" v-if="view === 1">
            <div class="envelope" ref="reservation2envelope">
                <h2>{{calendarTimeInitData.translation.header}}</h2>
                <div class="info-form">
                  <div class="form-element table">
                    <h4>{{calendarTimeInitData.translation.table}}</h4>
                    <h4>№ {{table}} </h4>
                  </div>
                  <div class="form-element guests" >
                    <h4>{{calendarTimeInitData.translation.for}}</h4>
                    <span> <input 
                      type="number"
                      v-model="persons" 
                      :max="maxPersons"
                      min="1"                     
                      > 
                      <h4>{{calendarTimeInitData.translation.people}}</h4> 
                    </span>
                  </div>
                  <div class="form-element date">
                    <h4>{{calendarTimeInitData.translation.on}}</h4>
                    <h4>{{dateForClient}}</h4>
                  </div>
                  <div class="form-element from">
                    <h4>{{calendarTimeInitData.translation.from}}</h4>
                    <h4>{{timeStart}}</h4>
                  </div>
                  <div class="form-element cafe">
                    <h4>{{calendarTimeInitData.translation.in}}</h4>
                    <h4>{{cafeName}}</h4>
                  </div>
                  <a class="c0ffee-button" id="change" @click="change">{{calendarTimeInitData.translation.changeButton}}</a>
                </div>
                

                <div class="input-form">
                  <div class="input-element name">
                    <input
                      class="name"
                      :placeholder="calendarTimeInitData.translation.name"
                      v-model="name"
                      type="text"
                    />
                    <span 
                      class="trem-icon tremtr-icon-uniF101"
                      v-show="(this.name === '')"

                    ></span>
                    <span 
                      class="trem-icon tremtr-icon-uniF101"
                      v-show="(this.name != '')"
                      style="color: green;"
                    ></span>
                  </div>

                  <div class="input-element email">
                    <input
                      name="email" 
                      v-model="email" 
                      v-validate="'required|email'" 
                      :class="{'input': true, 'is-danger': errors.has('email') }" 
                      type="email" 
                      :placeholder="calendarTimeInitData.translation.email"
                    >
                    <span 
                      class="trem-icon tremtr-icon-uniF10B"
                      v-show="errors.has('email')" 
                      style="color: red;"
                    ></span>
                    <span 
                      class="trem-icon tremtr-icon-uniF10B"
                      v-show="!errors.has('email') && (this.email != '')" 
                      style="color: green;"
                    ></span>
                    <span 
                      class="trem-icon tremtr-icon-uniF10B"
                      v-show="!errors.has('email') && (this.email === '')" 
                      
                    ></span>
                  </div>

                  <div class="input-element phone">
                    <input 
                      name="phone" 
                      v-model="phone" 
                      v-validate="{ rules: { regex: /^[0-9 ()+-]+$/} }" 
                      :class="{'input': true, 'is-danger': errors.has('phone') }" 
                      type="text" 
                      :placeholder="calendarTimeInitData.translation.phone"
                    >

                    <span 
                      class="trem-icon tremtr-icon-uniF105"
                      v-show="errors.has('phone')" 
                      style="color: red;"
                    ></span>
                    <span 
                      class="trem-icon tremtr-icon-uniF105"
                      v-show="!errors.has('phone') && (this.phone != '')" 
                      style="color: green;"
                    ></span>
                    <span 
                      class="trem-icon tremtr-icon-uniF105"
                      v-show="!errors.has('phone') && (this.phone === '')" 
                      
                    ></span>
                  </div>
                  
                  <div class="input-element message">
                    <textarea
                      rows="5"
                      class="message"
                      :placeholder="calendarTimeInitData.translation.message"
                      v-model="message"
                      type="text"
                      wrap="hard"
                    ></textarea>

                    <span 
                      class="trem-icon tremtr-icon-uniF109"
                      v-show="this.message !== ''" 
                      style="color: green;"
                    ></span>
                    <span 
                      class="trem-icon tremtr-icon-uniF109"
                      v-show="this.message === ''" 
                      
                    ></span>
                  </div>
                  <a class="c0ffee-button" id="confirm" @click="confirm">{{calendarTimeInitData.translation.confirmButton}}</a>
                </div>
            </div>
        </div>
      </transition>
      <transition name="fade" mode="in-out">
        <div class="reservation3" v-if="view === 2">
          <div class="confirmation">
            <h3>{{firstName}}</h3>
            <hr>
          </div>
        </div>
      </transition>
    </div>
    
</template>

<script>
import moment from 'moment'
import flatPickr from 'vue-flatpickr-component'

import 'flatpickr/dist/flatpickr.css'

import './assets/font/trem-reservation/css/trem-reservation.css'

import FlatpickrI18n from 'flatpickr/dist/l10n'

import { Carousel, Slide } from 'vue-carousel';

export default {
  name: 'reservation',
  components: {
    flatPickr,
    moment,
    Carousel,
    Slide
  },
  props: {
    getView: {
      default: 0
    }
  },
  data () {
    return {
      date: '',
      dateConfig: '',
      persons: '',
      maxPersons: '',
      timeStart: '',
      timeEnd: '',
      openHoursStart: '',
      openHoursEnd: '',
      table: '',
      name: '',
      cafeName: '',
      message: '',
      email: '',
      phone: '',
      userSurname: '',
      view: 0,
      canvas: '',
      width: 0,
      canvasInitData: '',
      reservations: '',
      disabledTables: [''],
      toast: '',
      calendarTimeInitData: tremtr_data,
      weekDays1: ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"],
      weekDays0: ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"],
      trueTimeFormat: '',
      trueDateFormat: '',
      dbDateFormatForMoment: 'YYYY/MM/DD',
      dbTimeFormatForMoment: 'HH:mm',
      canvasLoaded: false,
      windowWidth: 0,
      windowHeight: 0,
      peopleFormHeight: 367,
      reservationOne: [],
      reservationTwo: [],
      arrayOfWorkingTimes: [' '],
      caruselNavNext: '👉',
      caruselNavPrev: '👈',
      clickedTimes: [],

      zoomStartScale: 0,
      panning: false,
      draglastX: 0,
      draglastY: 0,
      dragCurrentX: 0,
      dragCurrentY: 0,
      canvasMinZoom: 0,
      canvasMaxZoom: 0,
      canvasZoomedWidth: 0,
      canvasZoomedHeight: 0,
      canvasImageWidth: 0,
      canvasImageHeight: 0,
      canvasAllowedXPan: 0,
      canvasAllowedYPan: 0,
      canvasLastPinchScale: 0,
      pausePanning: false,
      afterMindnight: false,

      disabledDatesFormatted: [],
      disabledDaysOfWeek: [],

    }
  },

  beforeMount: function () {
    this.width = window.innerWidth;

    //for flatpickr
    this.trueTimeFormat = this.pickadateToFlatPickrFormat(this.calendarTimeInitData.time_format) 
    this.trueDateFormat = this.pickadateToFlatPickrFormat(this.calendarTimeInitData.date_format)
    this.makeDateConfig() 

  },

  mounted: function () {

    this.$nextTick(function() {
        window.addEventListener('resize', this.getWindowWidth);
        window.addEventListener('resize', this.getWindowHeight);
    })

    this.getWindowWidth()
    this.getWindowHeight()

    //important to localize before canvas init
    moment.locale(this.calendarTimeInitData.translation.calendar)

    this.initCanvas()
  
    document.getElementById('reservation').style.setProperty('--button-color', this.calendarTimeInitData.mainColor)

  }, 

  computed: {

    dateForClient: function() { 

      if(this.openHoursStart._i.substr(0, this.openHoursStart._i.indexOf(':')).length === 1){
        this.openHoursStart._i = "0" + this.openHoursStart._i
      }

      if(moment(this.timeStart, this.momentTimeFormat).format(this.dbTimeFormatForMoment) < this.openHoursStart._i){
        this.afterMindnight = true
        return moment(this.date, this.momentDateFormat).add(1, 'day').format(this.momentDateFormat)
      }
      this.afterMindnight = false
      return this.date
    },

    fillActive: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillActive +',0.4)'
    },

    activeStroke: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillActive +'0)'
    },

     fillHover: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillActive +',0.9)'
    },

     fillBooked: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillBooked +',0.4)'
    },

    bookedFrame: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillBooked +',1)'
    },
    
    envelopeHeight: function() {
      if (this.canvas !== null) {
        return (this.canvas.height + this.peopleFormHeight).toString() + 'px'
      }
    },

    momentTimeFormat: function() {
      if (this.trueTimeFormat) {
        return this.flatPickrToMomentTimeFormat(this.trueTimeFormat)
      }
    },

    momentDateFormat: function() {
      if (this.trueDateFormat) {
        return this.flatPickrToMomentDateFormat(this.trueDateFormat)
      }
    },

    viewC: function () {
      return this.getView
    },

    isDissableDate: function () {
      return (this.date === '')
    },

    isDissableStartTime: function () {
      return (this.timeStart === '' || this.date === '')
    },

    dayOfWeek: function () {
      let dayOfWeek = moment(this.date, this.momentDateFormat).locale('en').format('dddd').toLowerCase()
      moment.locale(this.calendarTimeInitData.translation.calendar)
      return dayOfWeek    
    },

    selectable: function () {
      if (this.canvas !== ''){
        if ((this.date !== '') && (this.timeEnd !== '') && (this.timeStart !== '')) {
          this.makeTablesSelectable()
        }
      }
    },

    firstName: function () {
      if (this.name !== ''){
        if (this.name.indexOf(' ') >= 0) {

          return this.name.substr(0,this.name.indexOf(' ')) + ', ' + this.calendarTimeInitData.translation.thanksAtTheEnd
        } else {

          return this.name + ', ' + this.calendarTimeInitData.translation.thanksAtTheEnd
        }
      }
    }

  },

  watch: {

    date: function (newDate) {

      //reset times
      this.arrayOfWorkingTimes = []
      this.clickedTimes.map(val => val.className -= " carusel-active-item")
      this.clickedTimes = []

      

      if (this.isDissableDate) {
        this.timeStart = ''
        this.openHoursStart = ''
        this.openHoursEnd = ''

        //reset times
        this.arrayOfWorkingTimes = []

        // whenever question changes, this function will run
        this.makeTablesSelectable()
      }  else {

        //clean times
        this.timeStart = ''
        this.timeEnd = ''

        //init openHoursStart and openHoursEnd
        let timeFinish = ''
        let timeStart = ''
        for (let open of this.calendarTimeInitData.schedule_open){
          let keyNames = Object.keys(open.weekdays);
          for (let i of keyNames) {
            if (i === this.dayOfWeek) {
              timeFinish = open.time.end;
              timeStart = open.time.start;
            }
          }
        }
        for (let open of this.calendarTimeInitData.schedule_closed){
          moment.locale(this.calendarTimeInitData.translation.calendar)
          if (open.date === moment(this.date, this.momentDateFormat).format(this.dbDateFormatForMoment)) { 
            timeStart = open.time.start
            timeFinish = open.time.end 
          }
        }

        this.openHoursStart = timeStart;
        this.openHoursEnd = timeFinish;

        //expand times to time/date for case of working after 12pm
        const momentDate = moment(this.date, this.momentDateFormat)
        this.openHoursStart = moment(timeStart, this.dbTimeFormatForMoment).set({
          date: momentDate.date(),
          month: momentDate.month(),
          year: momentDate.year()
        })
        this.openHoursEnd = moment(timeFinish, this.dbTimeFormatForMoment).set({
          date: momentDate.date(),
          month: momentDate.month(),
          year: momentDate.year()
        })
        if (moment(timeFinish, this.dbTimeFormatForMoment).diff(moment(timeStart, this.dbTimeFormatForMoment)) < 0) {
          this.openHoursEnd.add(1, 'd')
        }
      

        //restrict reservation hours for "today"
        moment.locale(this.calendarTimeInitData.translation.calendar)
        if (moment().date() === moment(this.date, this.momentDateFormat).date()) {
          let time = this.openHoursStart
          
          if (moment().diff(time)) {
            time = moment()
            let addMinutes = Number(this.calendarTimeInitData.late_reservations) 
            time.add(addMinutes, 'm')
            let roundTime = Number(time.minutes()) % Number(this.calendarTimeInitData.time_interval) 
            time.add(Number(this.calendarTimeInitData.time_interval) -roundTime, 'm')
            
            if (time.diff(this.openHoursEnd) > 0) { 
              this.date = ''
            }
            this.openHoursStart.set({  
              hour: time.hour(),
              minute: time.minute()
            })
          }
        }

        //Prevent make reservation in last minute before closing
        if (this.openHoursStart && this.openHoursEnd){
          const timeBegin = this.openHoursStart
          const timeEnd = this.openHoursEnd.add(Number(-this.calendarTimeInitData.reservation_duration), 'm') 
          this.arrayOfWorkingTimes = []

          let counter = 0
          while (timeBegin.diff(timeEnd) <= 0) {
            this.arrayOfWorkingTimes.push(timeBegin.format(this.momentTimeFormat))
            timeBegin.add(Number(this.calendarTimeInitData.time_interval), 'm')
            counter++
          }
          timeBegin.add(-counter*Number(this.calendarTimeInitData.time_interval), 'm')
        }
      } 
    },


    timeStart: function (newTimeStart) {

      //set time end
      if (newTimeStart !== '') {
        this.timeEnd = moment(newTimeStart, this.momentTimeFormat).add(Number(this.calendarTimeInitData.reservation_duration), 'm').format(this.momentTimeFormat)
        this.renewDisabledTables ()
      } else {
        this.timeEnd = ''
      }
    },

    timeEnd: function (newTimeEnd) {
      this.makeTablesSelectable()
    },

  },

  methods: {

    selectedTime(e) {
      this.clickedTimes.map(val => val.className -= " carusel-active-item")
      this.clickedTimes = []
      this.clickedTimes.push(e.target)
      this.timeStart = e.target.innerHTML
      e.target.className += " carusel-active-item"
    },

    makeDateConfig() {

      let disabledDates = []


      //disable all closed dates from exceptions
      moment.locale(this.calendarTimeInitData.translation.calendar)
      for (let closed of this.calendarTimeInitData.schedule_closed){
        if (closed.time === undefined) {
          disabledDates.push(moment(closed.date, this.dbDateFormatForMoment))
        }
      }

      for(let d of disabledDates){
         this.disabledDatesFormatted.push(d.format(this.momentDateFormat))
      }

      //translate and set first day of week (e.g. Localization)
      let locale = ''
      if (this.calendarTimeInitData.translation.calendar !== 'en') {
        locale = FlatpickrI18n[this.calendarTimeInitData.translation.calendar]
        locale.firstDayOfWeek  = this.calendarTimeInitData.week_start
      } else {
         locale = {firstDayOfWeek: this.calendarTimeInitData.week_start}
      }
      

      //disable all days of week without scheduling
      let timeFinish = ''
      let timeStart = ''

      for (let open of this.calendarTimeInitData.schedule_open){
        let keyNames = Object.keys(open.weekdays);

        if (this.calendarTimeInitData.week_start === '0') {

          for (let i of this.weekDays0) {
            if (keyNames.includes(i)) {
              this.disabledDaysOfWeek.push(this.weekDays0.indexOf(i)); //this.disabledDaysOfWeek means enabledDaysOfWeek 😑
            }
          }
        }

        if (this.calendarTimeInitData.week_start === '1') {

          for (let i of this.weekDays1) {
            if (keyNames.includes(i)) {
              this.disabledDaysOfWeek.push(this.weekDays0.indexOf(i));
            }
          }
        }
      }

      this.dateConfig = {
        defaultDate: null,
        disableMobile: true,
        dateFormat: this.trueDateFormat,
        locale: locale,
        minDate: "today",
        maxDate: new Date().fp_incr(Number(this.calendarTimeInitData.early_reservations)),
        disable: [
            (date) => {

              let workingDaysFromException = true
              // if there is that day in exceptions but not closed full day
              if (this.calendarTimeInitData.schedule_closed !== '0') {
                workingDaysFromException = !Boolean(this.calendarTimeInitData.schedule_closed.filter( close => 
                  (
                    moment(date).format(this.dbDateFormatForMoment) === close.date
                    &&
                    close.time !== undefined                  
                  )
                ).length 
              )}
                            
              return (
                (!(this.disabledDaysOfWeek.includes(date.getDay())) || // if for this day not setted a shedule
                this.disabledDatesFormatted.includes(moment(date).format(this.momentDateFormat)) ) && // if there is that day in exceptions for all day (e.g. not working all day)
                workingDaysFromException) // if there is that day in exceptions but not closed full day
            }
        ]
      }
    },

    computeWokingtimes() {
      this.windowWidth = document.getElementById("reservation").parentNode.parentElement.clientWidth;
    },

    getWindowWidth(event) {
      this.windowWidth = document.getElementById("reservation").parentNode.parentElement.clientWidth;
    },

    getWindowHeight(event) {
      this.windowHeight = document.documentElement.clientHeight;
    },

    flatPickrToMomentTimeFormat (str) {
      let formattedStr = str;
      formattedStr = formattedStr.replace('K' , 'A')
      formattedStr = formattedStr.replace('H' , 'HH')
      formattedStr = formattedStr.replace('i' , 'mm')
      return (formattedStr)
    },

    flatPickrToMomentDateFormat (str) {
      let formattedStr = str;

      if (formattedStr.match(/d/g) !== null) {
        formattedStr = formattedStr.replace('d' , 'DD')

      } else if (formattedStr.match(/D/g) !== null) {
        formattedStr = formattedStr.replace('D' , 'ddd')

      } else if (formattedStr.match(/l/g) !== null) {
        formattedStr = formattedStr.replace('l' , 'dddd')

      } else if (formattedStr.match(/j/g) !== null) {
        formattedStr = formattedStr.replace('j' , 'D')
      } 


      if (formattedStr.match(/m/g) !== null) {
        formattedStr = formattedStr.replace('m' , 'MM')

      } else if (formattedStr.match(/n/g) !== null) {
        formattedStr = formattedStr.replace('n' , 'M')

      } else if (formattedStr.match(/M/g) !== null) {
        formattedStr = formattedStr.replace('M' , 'MMM')

      } else if (formattedStr.match(/F/g) !== null) {
        formattedStr = formattedStr.replace('F' , 'MMMM')
      } 

      formattedStr = formattedStr.replace('Y' , 'YYYY')
      return (formattedStr)
    },

    pickadateToFlatPickrFormat (str) {
      let formattedStr = str;
      if (formattedStr.match(/dddd/g) !== null) {
        formattedStr = formattedStr.replace('dddd' , 'l')

      } else if (formattedStr.match(/ddd/g) !== null) {
        formattedStr = formattedStr.replace('ddd' , 'D')

      } else if (formattedStr.match(/dd/g) !== null) {
        formattedStr = formattedStr.replace('dd' , 'd')

      } else if (formattedStr.match(/d/g) !== null) {
        formattedStr = formattedStr.replace('d' , 'j')
      } 

      if (formattedStr.match(/mmmm/g) !== null) {
        formattedStr = formattedStr.replace('mmmm' , 'F')

      } else if (formattedStr.match(/mmm/g) !== null) {
        formattedStr = formattedStr.replace('mmm' , 'M')

      } else if (formattedStr.match(/mm/g) !== null) {
        formattedStr = formattedStr.replace('mm' , 'm')

      } else if (formattedStr.match(/m/g) !== null) {
        formattedStr = formattedStr.replace('m' , 'n')
      }

      formattedStr = formattedStr.replace('yyyy' , 'Y')

      formattedStr = formattedStr.replace('HH' , 'H')
      formattedStr = formattedStr.replace('A' , 'K')
      return (formattedStr)
    },

    flatPickrToPickadateFormat (str) {
      let formattedStr = str;

      if (formattedStr.match(/l/g) !== null) {
        formattedStr = formattedStr.replace('l' , 'dddd')

      } else if (formattedStr.match(/D/g) !== null) {
        formattedStr = formattedStr.replace('D' , 'ddd')

      } else if (formattedStr.match(/d/g) !== null) {
        formattedStr = formattedStr.replace('d' , 'dd')

      } else if (formattedStr.match(/j/g) !== null) {
        formattedStr = formattedStr.replace('j' , 'd')
      } 

      if (formattedStr.match(/F/g) !== null) {
        formattedStr = formattedStr.replace('F' , 'mmmm')

      } else if (formattedStr.match(/M/g) !== null) {
        formattedStr = formattedStr.replace('M' , 'mmm')

      } else if (formattedStr.match(/m/g) !== null) {
        formattedStr = formattedStr.replace('m' , 'mm')

      } else if (formattedStr.match(/n/g) !== null) {
        formattedStr = formattedStr.replace('n' , 'm')
      }

      formattedStr = formattedStr.replace('Y' , 'yyyy')

      formattedStr = formattedStr.replace('H' , 'HH')
      formattedStr = formattedStr.replace('K' , 'A')
      return (formattedStr)
    },

    book () {
      if ((this.date != '') && (this.timeStart != '') && (this.timeFinish != '') && (this.table != '')) {


        let elementRes = document.getElementById('reservation')
        let offsetHeight = elementRes.offsetHeight

        this.$refs.reservationOne.style.display = 'none';
        this.view = 1

        setTimeout(function () {
          if ((this.canvas.height + this.peopleFormHeight) < this.$refs.reservation2envelope.clientHeight) {
            this.$refs.tremReservation.style.height = (this.$refs.reservation2envelope.clientHeight + 30).toString() + 'px'
          }
        }.bind(this), 500)


        if (this.windowHeight*1.5 < offsetHeight) {
          //var e = document.getElementsByClassName("cap")  + e[0].offsetHeight
          window.scrollTo(0, elementRes.offsetTop)
        }
        
      } else {
        this.$toasted.show(this.calendarTimeInitData.translation.bookTableButtonWarning, { 
          theme: "primary", 
          position: "top-center", 
          duration : 3000,
          className: 'toast',
          containerClass: 'toast-container'
        });
      }
    },

    confirm () {
      if ((this.name !== '') && (this.mail !== '') && (this.phone !== '') && (!this.errors.has('email')) && (!this.errors.has('phone'))) {
        moment.locale(this.calendarTimeInitData.translation.calendar)
        
        let date = null
        if (this.afterMindnight) {
          date = moment(this.dateForClient, this.momentDateFormat).format(this.dbDateFormatForMoment)
        } else {
          date = moment(this.date, this.momentDateFormat).format(this.dbDateFormatForMoment)
        }
        
        this.$http.post(this.calendarTimeInitData.url, {
          'action': 'tremtr_reservation',
          'nonce': this.calendarTimeInitData.nonce,
          'tremtr_reservation_date': date,
          'tremtr_reservation_time_begin': moment(this.timeStart, this.momentTimeFormat).format(this.dbTimeFormatForMoment),
          'tremtr_reservation_time_end': moment(this.timeEnd, this.momentTimeFormat).format(this.dbTimeFormatForMoment),
          'tremtr_reservation_table': this.table,
          'tremtr_reservation_name': this.name,
          'tremtr_reservation_persons': this.persons,
          'tremtr_reservation_email': this.email,
          'tremtr_reservation_phone': this.phone,
          'tremtr_reservation_message': this.message
        },
        {
          emulateJSON: true
        }).then(response => {

          if (JSON.parse(response.bodyText).success === true) {

            this.$refs.reservationTwo.style.display = 'none'
            this.view = 2

            setTimeout(function () {
              this.hideReservation()
            }.bind(this), 3000)

          } else {

            this.$toasted.show(this.calendarTimeInitData.translation.rejected, { 
              theme: "primary", 
              position: "top-center", 
              duration : 7000,
              className: 'toast',
              containerClass: 'toast-container'
            });

            this.$http.get(this.calendarTimeInitData.endpoint_reservation).then(response => {

              // get body data 
              this.reservations = response.body
              for (let reservation of this.reservations){
                reservation.tremtr_reservation_date = moment(reservation.tremtr_reservation_date, this.dbDateFormatForMoment).format(this.momentDateFormat)
                reservation.tremtr_reservation_time_begin = moment(reservation.tremtr_reservation_time_begin, this.dbTimeFormatForMoment).format(this.momentTimeFormat)
                reservation.tremtr_reservation_time_end = moment(reservation.tremtr_reservation_time_end, this.dbTimeFormatForMoment).format(this.momentTimeFormat)
              }

            }, response => {

              // error callback 
            });
          }
          

        }, response => {
          // error callback
        });

        
      } else {
        this.$toasted.show(this.calendarTimeInitData.translation.confirmButtonWarning, { 
          theme: "primary", 
          position: "top-center", 
          duration : 3000,
          className: 'toast',
          containerClass: 'toast-container'
        });
      }
    },
    change () {

      this.$refs.reservationTwo.style.display = 'none';
      this.view = 0

      setTimeout(function () {
        this.$refs.tremReservation.style.height = this.envelopeHeight
      }.bind(this), 500)

    },
    changeDate () {
      this.date = ''
    },
    hideReservation () {
      this.view = 4
    },

    switchReservation () {
      if (this.view < 4) {
        this.view = 4
        this.activeButton.active = false
      } else {
        this.view = 0
        this.activeButton.active = true
      }
    },

    initCanvasObject (obj){

      obj.lockMovementX = true; 
      obj.lockMovementY = true; 
      obj.lockScalingX = true;
      obj.lockScalingY = true;
      obj.lockUniScaling = true;
      obj.lockRotation = true; 
      obj.hasControls = false;
      obj.hoverCursor = 'pointer';
      obj.hasBorders = false;
      obj.opacity = 0;
      obj.selectable = false;
      obj.evented = false;
    },

    initCanvas () {

      let fillActive = this.fillActive
      let fillHover = this.fillHover
      let fillBooked = this.fillBooked


      this.$http.get(this.calendarTimeInitData.endpoint_cafe).then(response => {

        // get body data 
        this.canvasInitData = response.body[0].tremtr_content
        this.cafeName = response.body[0].title.rendered

        this.canvas = new fabric.Canvas('cc', { 
          selection: false,
          controlsAboveOverlay:true,
          centeredScaling:true,
          allowTouchScrolling: true
        })

        this.canvas.loadFromJSON(this.canvasInitData, () => {

          let parsedInfo =  JSON.parse(this.canvasInitData);


          if (parsedInfo.backgroundImage == null ){

            this.canvas.setHeight(300)
            this.canvas.setWidth(200)

            this.$toasted.show("ADD IMAGE!", { 
              theme: "bubble", 
              position: "top-center", 
              duration : 30000,
              className: 'toast',
              containerClass: 'toast-container'
            });

            this.canvas.renderAll()

          } else {

            let width = this.windowWidth;
            if (width > parsedInfo.backgroundImage.width) {
              width = parsedInfo.backgroundImage.width;
            }


            let scale = (width*0.9)/(parsedInfo.backgroundImage.width*parsedInfo.backgroundImage.scaleX);
            this.canvas.setZoom(scale)
            
            this.canvas.renderAll()

            const h = parsedInfo.backgroundImage.height * parsedInfo.backgroundImage.scaleY * scale;
            const w = parsedInfo.backgroundImage.width * parsedInfo.backgroundImage.scaleX * scale;

            this.canvas.setHeight(h)
            this.canvas.setWidth(w)

            this.canvas.renderAll()

            if (h < 200) {

              this.canvas.zoomToPoint({x: w/2, y: h/2}, scale*2.5)
              this.canvas.setHeight(400)
            }

            this.canvasImageWidth = w / scale
            this.canvasImageHeight = h / scale
            this.canvasAllowedXPan = w / scale
            this.canvasAllowedYPan = h / scale

            this.canvasMinZoom = this.canvas.getZoom()
            this.canvasMaxZoom = this.canvas.getZoom()*2
            this.canvasZoomedWidth = this.canvasImageWidth
            this.canvasZoomedHeight = this.canvasImageHeight

          }

          //add id to canvas objects and lock objects
          
          let objCounter = 1;
          let counter = 0;
          let canvasObjectId = 0;

          for(let i = 0; i < this.canvas.getObjects().length; i+=3){
            
            this.canvas.item(i).id = this.canvas.item(i + 1).text;
            this.canvas.item(i + 1).id = this.canvas.item(i + 1).text
            this.canvas.item(i + 2).id = this.canvas.item(i + 1).text

            this.initCanvasObject (this.canvas.item(i))
            this.initCanvasObject (this.canvas.item(i + 1))
            this.initCanvasObject (this.canvas.item(i + 2))
            
            let tablePeople = [this.canvas.item(i+1).text, this.canvas.item(i+2).text]
            this.canvas.item(i).name = tablePeople;

            this.canvas.item(i).set({
              fill: fillActive,
              strokeWidth : 0,
              opacity: 1
            });

            this.canvas.item(i+1).set({
              opacity: 1,
              left: this.canvas.item(i).left + 10,
              top: this.canvas.item(i).top + 10,
              fontSize: 20,
              fontWeight: 'bold'
            });

            this.canvas.item(i+2).set({
              text: ''
            });

            this.canvas.renderAll()
          }

          var _self = this;
          let called = false //for toasts

          this.canvas.on('mouse:over', function(e) {


            if (e.target != null) {
              if ((e.target.fill === fillActive) && (e.target.type === 'rect')) {
                e.target.set({
                  fill: fillHover
                });
              }
              e.target.canvas.renderAll();
            }
          });


          this.canvas.on('mouse:out', function(e) {
            if (e.target != null) {
              if ((e.target.fill === fillHover)  && (e.target.type === 'rect')) {
                if (_self.canvas.item(_self.canvas.getObjects().indexOf(e.target)+2).opacity != 1) {
                  e.target.set({
                    fill: fillActive
                  });

                }
              }
              e.target.canvas.renderAll();
            }
          });


          
          _self.canvas.observe('mouse:down',function(e) {

            if (((_self.date === '') || (_self.timeEnd === '') || (_self.timeStart === '')) && !(called)) {

              _self.$toasted.show(_self.calendarTimeInitData.translation.canvasClickWarning, { 
                theme: "primary", 
                position: "top-center", 
                duration : 2000,
                className: 'toast',
                containerClass: 'toast-container'
              });

              called = !called
              setTimeout(function(){
                called = !called
              }, 5000)
            }

            if (e.target != null) {

              for(let i = 0; i < _self.canvas.getObjects().length; i++){
                if (_self.canvas.item(i).fill === fillHover){
                  _self.canvas.item(i).set({
                    fill: fillActive
                  });
                  _self.canvas.item(i+2).set({
                    opacity: 0
                  });
                }
              }

              if (e.target.type === 'rect') {
                let index = _self.canvas.getObjects().indexOf(e.target)

                e.target.set({
                  fill: fillHover
                });

                _self.canvas.item(_self.canvas.getObjects().indexOf(e.target)+2).set({
                  opacity: 1
                });
                
                _self.table = e.target.name[0];
                _self.persons = e.target.name[1];
                _self.maxPersons = e.target.name[1];
              }
              
              if (e.target.type === 'text') {
                let index = _self.canvas.getObjects().indexOf(e.target)

                _self.canvas.item(index-1).set({
                  fill: fillHover
                });

                _self.table = _self.canvas.item(index-1).name[0];
                _self.persons = _self.canvas.item(index-1).name[1];
                _self.maxPersons = _self.canvas.item(index-1).name[1];
              }
              
              _self.canvas.renderAll()
            }
          });

          this.canvas.on({
              'touch:gesture': event => {

                if (event.e.touches && event.e.touches.length == 2) {
                  this.pausePanning = true;
                  let point = new fabric.Point(this.canvas.getCenter().left, this.canvas.getCenter().top);
                  if (event.self.state == "start") {
                    this.zoomStartScale = this.canvas.getZoom();
                  }
                  let delta = this.zoomStartScale * event.self.scale;
                  if (delta < this.canvasMaxZoom && delta > this.canvasMinZoom) {
                    this.canvas.zoomToPoint(point, delta);
                    this.canvasZoomedWidth = this.canvas.getZoom() * this.canvasImageWidth / this.canvasMinZoom
                    this.canvasZoomedHeight = this.canvas.getZoom() * this.canvasImageHeight / this.canvasMinZoom
                    this.canvasAllowedXPan = this.canvasZoomedWidth - this.canvas.getWidth()
                    this.canvasAllowedYPan = this.canvasZoomedHeight - this.canvas.getHeight()
                    //this.pausePanning = false;
                  } 
                  this.pausePanning = false;
                  if (typeof event.e.stopPropagation === "function") {
                    event.e.stopPropagation();
                    event.e.preventDefault();
                    return false;
                  }
                }
              },
              'object:selected': event => {
                this.pausePanning = true;
              },
              'selection:cleared': event => {
                this.pausePanning = false;
              },
              'touch:drag': event => {

                if (this.pausePanning == false && event.e.touches != null ) {
                    this.dragCurrentX = event.e.touches[0].pageX;
                    this.dragCurrentY = event.e.touches[0].pageY;
                    const xChange = this.dragCurrentX - this.draglastX;
                    const yChange = this.dragCurrentY - this.draglastY;
                    
                    if((Math.abs(this.dragCurrentX - this.draglastX) <= 50) && (Math.abs(this.dragCurrentY - this.draglastY) <= 50)) {
                       
                        if ((Math.abs(this.canvas.viewportTransform[4]) <= this.canvasAllowedXPan) && (Math.abs(this.canvas.viewportTransform[5]) <= this.canvasAllowedYPan)) {
                          this.canvas.relativePan({x: xChange,y: yChange})
                        } else {
                          if (Math.abs(this.canvas.viewportTransform[4]) > this.canvasAllowedXPan) {
                            if (this.canvas.viewportTransform[4] > 0) {
                              this.canvas.viewportTransform[4] = this.canvasAllowedXPan
                            } else {
                              this.canvas.viewportTransform[4] = -this.canvasAllowedXPan
                            }
                          } else {
                            if (this.canvas.viewportTransform[5] > 0) {
                              this.canvas.viewportTransform[5] = this.canvasAllowedYPan
                            } else {
                              this.canvas.viewportTransform[5] = -this.canvasAllowedYPan
                            }
                          }
                        }
                    }
                    this.draglastX = this.dragCurrentX;
                    this.draglastY = this.dragCurrentY;
                }
                
                if (typeof event.e.stopPropagation === "function") {
                  event.e.stopPropagation();
                  event.e.preventDefault();
                  return false;
                }
              }
            });

          this.canvasLoaded = true
        });
      }, response => {
        // error callback 
      });

      this.$http.get(this.calendarTimeInitData.endpoint_reservation).then(response => {

        // get body data 
        this.reservations = response.body
        for (let reservation of this.reservations){
          reservation.tremtr_reservation_date = moment(reservation.tremtr_reservation_date, this.dbDateFormatForMoment).format(this.momentDateFormat)
          reservation.tremtr_reservation_time_begin = moment(reservation.tremtr_reservation_time_begin, this.dbTimeFormatForMoment).format(this.momentTimeFormat)
          reservation.tremtr_reservation_time_end = moment(reservation.tremtr_reservation_time_end, this.dbTimeFormatForMoment).format(this.momentTimeFormat)
        }

      }, response => {

        // error callback 
      });
    },

    renewDisabledTables () {

      this.disabledTables = []

      if (this.timeStart !== '' && this.timeEnd != '') {
        
        let todaysReservations = ''
        if (this.openHoursStart.format(this.momentDateFormat) === this.openHoursEnd.format(this.momentDateFormat)) {
          todaysReservations = this.reservations.filter(reservation => 
            ( 
              reservation.tremtr_reservation_date.toLowerCase() === this.date.toLowerCase() && 
              moment(reservation.tremtr_reservation_time_begin, this.momentTimeFormat).format(this.dbTimeFormatForMoment)  >= this.openHoursStart.format(this.dbTimeFormatForMoment)
            )
          )
        } else {
          todaysReservations = this.reservations.filter(reservation => {

            this.openHoursEnd.add(Number(this.calendarTimeInitData.reservation_duration), 'm') 

            let tmp = (
                        reservation.tremtr_reservation_date.toLowerCase() === moment(this.date, this.momentDateFormat).add(1, 'day').format(this.momentDateFormat).toLowerCase() &&
                        moment(reservation.tremtr_reservation_time_end, this.momentTimeFormat).format(this.dbTimeFormatForMoment) <= this.openHoursEnd.format(this.dbTimeFormatForMoment)
                      ) 
                      || 
                      ( 
                        reservation.tremtr_reservation_date.toLowerCase() === this.date.toLowerCase() && 
                        moment(reservation.tremtr_reservation_time_begin, this.momentTimeFormat).format(this.dbTimeFormatForMoment)  >= this.openHoursStart.format(this.dbTimeFormatForMoment)
                      )

            this.openHoursEnd.add(Number(-this.calendarTimeInitData.reservation_duration), 'm') 

            return tmp
          });
        }
        

        const momentDate = moment(this.dateForClient, this.momentDateFormat)

        let selectedTimeStart = moment(this.timeStart, this.momentTimeFormat).set({
          date: momentDate.date(),
          month: momentDate.month(),
          year: momentDate.year()
        })
        let selectedTimeEnd = moment(this.timeEnd, this.momentTimeFormat).set({
          date: momentDate.date(),
          month: momentDate.month(),
          year: momentDate.year()
        })


        if (moment(this.timeStart, this.momentTimeFormat).diff(moment(this.timeEnd, this.momentTimeFormat)) > 0) {
          selectedTimeEnd.add(1, 'd')
        }

        for (let todaysReservation of todaysReservations){
          let momentInitDateTimeBegin = moment(todaysReservation.tremtr_reservation_date, this.momentDateFormat)
          let timeBegin = moment(todaysReservation.tremtr_reservation_time_begin, this.momentTimeFormat).set({
            date: momentInitDateTimeBegin.date(),
            month: momentInitDateTimeBegin.month(),
            year: momentInitDateTimeBegin.year()
          })
          let timeEnd = moment(todaysReservation.tremtr_reservation_time_end, this.momentTimeFormat).set({
            date: momentInitDateTimeBegin.date(),
            month: momentInitDateTimeBegin.month(),
            year: momentInitDateTimeBegin.year()
          })

          if (moment(timeBegin.format(this.momentTimeFormat), this.momentTimeFormat).diff(moment(timeEnd.format(this.momentTimeFormat), this.momentTimeFormat)) > 0 ) {
            timeEnd.add(1, 'd')
          }


          if (
              (
                selectedTimeStart.diff(timeBegin) >= 0
                && 
                timeEnd.diff(selectedTimeEnd) >= 0
              ) 
              ||
              (
                timeBegin.diff(selectedTimeStart) >= 0
                &&
                selectedTimeEnd.diff(timeBegin) > 0
              )
              ||
              (
                timeEnd.diff(selectedTimeStart) > 0
                &&
                selectedTimeEnd.diff(timeEnd) >= 0
              )
            ) {
            this.disabledTables.push(todaysReservation.tremtr_reservation_table)        
          }
        }
      }
      this.disableTable()
    },

    disableTable () {
      
      for(let i = 0; i < this.canvas.getObjects().length; i= i+3){
        if (this.disabledTables.includes(this.canvas.item(i).name[0])) {
          this.canvas.item(i).set({
            fill: this.fillBooked,
            evented: false
          });

          if (this.canvas.item(i).name[0] === this.table) {
            this.canvas.item(i).set({
              fill: this.fillBooked,
              evented: false
            });
            this.canvas.item(i+2).set({
              opacity: 0
            });
            this.table = ''
          }
        }

        if (!(this.disabledTables.includes(this.canvas.item(i).name[0]))) {
          if (this.canvas.item(i).fill === this.fillBooked) {
            this.canvas.item(i).set({
              fill: this.fillActive,
              evented: true
            });
          }
        }

        
      }
      this.canvas.renderAll()
    },

    makeTablesSelectable () {

        if (this.canvas !== ''){
            if ((this.date !== '') && (this.timeEnd !== '') && (this.timeStart !== '')) {
              for(let i = 0; i < this.canvas.getObjects().length; i= i+3){

                if (!(this.disabledTables.includes(this.canvas.item(i).name[0]))) { 

                  this.canvas.item(i).set({
                    evented: true
                  });
                }

              }
              this.canvas.renderAll()
            } else {
              for(let i = 0; i < this.canvas.getObjects().length; i= i+3){


                this.canvas.item(i).set({
                    evented: false,
                    fill: this.fillActive
                });

                this.canvas.item(i+2).set({
                  opacity: 0
                });
                
                this.table = ''

              }
              this.canvas.renderAll()
            }
  
        }
      
     
    }
  },

  beforeDestroy() {
    window.removeEventListener('resize', this.getWindowWidth);
    window.removeEventListener('resize', this.getWindowHeight);
  }
}
</script>

<style lang="scss"> 

  

</style>


<style scoped>

  
</style>

