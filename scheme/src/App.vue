<template>
    <div id="reservation" class="trem-scheme" ref="tremReservation" v-if="view !== 4" :style="[canvasLoaded ? {height: envelopeHeight} : {}] ">
      <transition name="fade" mode="in-out">
        <div class="reservation1" ref="reservationOne" v-show="(view === 0) && (canvasLoaded === true)">
          <div class="envelope"  :style="[canvasLoaded ? {height: envelopeHeight} : ''] ">
            <h1>{{cafeName}}</h1>
            <ui-modal
                dismiss-on="close-button esc"
                ref="modalFree"
                :title="calendarTimeInitData.translation.modalFreeHeader"
                size="small"
                @close="modalClosed"
            >
              <div class="input-form">
                  <div class="time-persons-element">
                    <div class="timeStart">
                      <h4>Time Begin</h4>
                      <h3>{{timeStart}}</h3>
                    </div>
                    <div class="timeEnd">
                      <h4>Time End</h4>
                      <h3>{{timeEnd}}</h3>
                    </div>
                    <div class="persons">
                      <h4>{{calendarTimeInitData.translation.people}}</h4> 
                      <input 
                        type="number"
                        v-model="persons" 
                        :max="maxPersons"
                        min="1"                     
                      > 
                    </div>
                  </div>

                  <div class="input-element timeSlider">
                    <label for="vue-slider-time-end">{{calendarTimeInitData.translation.stayTime}}</label>
                    <vue-slider 
                      id="vue-slider-time-end"
                      
                      ref="slider" 
                      v-if="timeSliderShow"
                      v-model="sliderValue" 
                      :min="sliderMin" 
                      :max="sliderMax"
                      :width="250"
                      :height="8"
                      :interval="5"
                      tooltip="false"
                      :formatter="sliderTooltipFormat"
                    >
                    </vue-slider>
                  </div>


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
                  <div class="input-element confirm">
                    <ui-button  id="confirm" @click="confirm" color="green" v-if="isTableInDisabledTables">{{calendarTimeInitData.translation.confirmButton}}</ui-button>
                    <h4 v-else>{{calendarTimeInitData.translation.tableBooked}}</h4>
                  </div>
                </div>
            </ui-modal>

            <ui-modal
                dismiss-on="close-button esc"
                ref="modalReserved"
                :title="calendarTimeInitData.translation.modalReservedHeader"
                size="small"
            >
              <div class="input-form">
                <div class="time-persons-element">
                  <div class="timeStart">
                    <h4>Time Begin</h4>
                    <h3>{{modalReservedData.timeStart}}</h3>
                  </div>
                  <div class="timeEnd">
                    <h4>Time End</h4>
                    <h3>{{modalReservedData.timeEnd}}</h3>
                  </div>
                  <div class="persons">
                    <h4>{{calendarTimeInitData.translation.people}}</h4> 
                    <h3>{{modalReservedData.persons}}</h3>
                  </div>
                </div>
              </div>

              <div class="info-form">
                <div class="form-element">
                  <h4>{{calendarTimeInitData.translation.name}}:</h4>
                  <h4>{{modalReservedData.name}}</h4> 
                </div>
                <div class="form-element">
                  <h4>{{calendarTimeInitData.translation.email}}:</h4>
                  <h4>{{modalReservedData.email}}</h4> 
                </div>
                <div class="form-element">
                  <h4>{{calendarTimeInitData.translation.phone}}:</h4>
                  <h4>{{modalReservedData.phone}}</h4> 
                </div>
                <div class="form-element">
                  <h4>{{calendarTimeInitData.translation.message}}:</h4>
                  <h4>{{modalReservedData.message}}</h4> 
                </div>

                <div class="delete">
                  <ui-button  id="confirm" @click="deleteReservation" color="red">{{calendarTimeInitData.translation.deleteButton}}</ui-button>
                </div>
              </div>
            </ui-modal>

            <div class="summary-table">
              <table>
                <tr>
                  <th>{{calendarTimeInitData.translation.time}}</th>
                  <th>{{calendarTimeInitData.translation.tables}}</th>
                </tr>
                <tr v-for="(value, time) of summaryTable" :key="time" >
                  <td>{{value.time}}</td>
                  
                  <td>{{value.tables}}</td>
                </tr>
              </table>
            </div>

            <div class="people-form" >
                <div class="form-element" id="dateinput">
                  <flat-pickr  :config="dateConfig" :placeholder="date" v-model="date" input-class="input"></flat-pickr>
                  <span class="trem-icon tremtr-icon-uniF10A" aria-hidden="true"></span>
                </div>
                <transition name="fade" mode="in-out">
                  <carousel v-show="(date !== '')" :navigationEnabled="true" :paginationEnabled="false" :perPage="3" :navigationNextLabel="caruselNavNext" :navigationPrevLabel="caruselNavPrev">
                    <slide v-for="(workingTime, i) of arrayOfWorkingTimes" :key="workingTime" ref="timeCarousel">
                      <div  @click="selectedTime($event)" :index="i">{{workingTime}}</div>
                    </slide>
                  </carousel>
                </transition>
                
                <canvas id="cc" class="context-menu-one" width="1000px" height="1000px" ></canvas>
            </div>
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

import vueSlider from 'vue-slider-component'

export default {
  name: 'reservation',
  components: {
    flatPickr,
    moment,
    Carousel,
    Slide,
    vueSlider
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
      siestaStart: '',
      siestaEnd: '',
      openHoursStart: '',
      openHoursEnd: '',
      table: '',
      tableModal: '',
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
      tables: [],
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

      isTableInDisabledTables: false,

      summaryTable: '',

      //for modalReserved
      modalReservedData: '',

      //for slider
      sliderActive: false,
      sliderValue: 0,
      sliderValueBeforeModalOpened: '',
      sliderMin: 0,
      sliderMax: 0,
      sliderTimeInterval: 1,
      sliderTooltipFormat: '',
      timeSliderShow: false

    }
  },

  created: function () {
    this.width = window.innerWidth;

    //for flatpickr
    this.trueTimeFormat = this.pickadateToFlatPickrFormat(this.calendarTimeInitData.time_format) 
    this.trueDateFormat = this.pickadateToFlatPickrFormat(this.calendarTimeInitData.date_format)
    this.makeDateConfig() 

    //important to localize before canvas init
    moment.locale(this.calendarTimeInitData.translation.calendar)

    this.initCanvas()

    this.infinityAutoRenew()

  },

  mounted: function () {

    this.$nextTick(function() {
        window.addEventListener('resize', this.getWindowWidth);
        window.addEventListener('resize', this.getWindowHeight);
    })

    this.getWindowWidth()
    this.getWindowHeight()

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

    dateDBFormat: function() { 
      return moment(this.date, this.momentDateFormat).format(this.dbDateFormatForMoment)
    },

    fillActive: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillActive +',0.4)'
    },

    activeStroke: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillActive +'0)'
    },

     fillActiveHover: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillActive +',0.9)'
    },

     fillBooked: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillBooked +',0.4)'
    },

    fillBookedHover: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillBooked +',0.9)'
    },

    bookedFrame: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillBooked +',1)'
    },
    
    envelopeHeight: function() {
      if (this.canvas !== null) {
        return '100%'
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
      this.disabledTables = []



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
        let siestaEnd = ''
        let siestaStart = ''
        let siestaIndex = 0; //just for siesta, order of ifs matter!
        for (let open of this.calendarTimeInitData.schedule_open){
          let keyNames = Object.keys(open.weekdays);
          for (let i of keyNames) {
            if (i === this.dayOfWeek) {

               if (i === this.dayOfWeek && siestaIndex === 1) {
              siestaEnd = open.time.start;
              siestaStart = timeFinish;
              timeFinish = open.time.end;
              siestaIndex++;
            }

            if (i === this.dayOfWeek && siestaIndex === 0) {
              timeFinish = open.time.end;
              timeStart = open.time.start;
              siestaIndex++;
            }
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
        if (siestaIndex === 2) {
          this.siestaStart = moment(siestaStart, this.dbTimeFormatForMoment).set({
            date: momentDate.date(),
            month: momentDate.month(),
            year: momentDate.year()
          })
          this.siestaEnd = moment(siestaEnd, this.dbTimeFormatForMoment).set({
            date: momentDate.date(),
            month: momentDate.month(),
            year: momentDate.year()
          })
        }
        
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
          
           if (moment().diff(time) > 0) {
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
          let siestaStart = ''
          let siestaEnd = ''
          if (siestaIndex === 2) {
            siestaStart = this.siestaStart.add(Number(-this.calendarTimeInitData.reservation_duration), 'm') 
            siestaEnd = this.siestaEnd
          }
          this.arrayOfWorkingTimes = []

          let counter = 0
          while (timeBegin.diff(timeEnd) <= 0) {
            if (siestaIndex === 2) {
              if (!(timeBegin.diff(siestaStart) >= 0 && timeBegin.diff(siestaEnd) < 0)){ //for siesta!
                this.arrayOfWorkingTimes.push(timeBegin.format(this.momentTimeFormat))
              }
            } else {
              this.arrayOfWorkingTimes.push(timeBegin.format(this.momentTimeFormat))
            }

            timeBegin.add(Number(this.calendarTimeInitData.time_interval), 'm')
            counter++
          }
          timeBegin.add(-counter*Number(this.calendarTimeInitData.time_interval), 'm')
          
        }

        this.renewSummaryTable()
      } 
    },


    timeStart: function (newTimeStart) {
      this.sliderValue = ''

      //set time end
      if (newTimeStart !== '') {
        this.initTimeEndSlider(newTimeStart)
      } else {
        this.timeEnd = ''
        this.sliderActive = false
      }
    },

    sliderValue: function (newSliderValue) {
      if (this.sliderActive) {
         this.timeEnd = moment(this.timeStart, this.momentTimeFormat).add(Number(newSliderValue), 'm').format(this.momentTimeFormat)
      }
    },

    timeEnd: function (newTimeEnd) {
      this.makeTablesSelectable()
      this.renewDisabledTables ()
    },

    disabledTables: function (newDisabledTables) {

      this.isTableInDisabledTables = !(newDisabledTables.includes(this.tableModal.toString()))
    },


  },

  methods: {

    infinityAutoRenew() {
      setInterval( () => {
        this.renewReservations()
      }, 300000);
    },

    renewSummaryTable(){

      this.summaryTable = []
      
      //just a good function
      const groupBy = (arr, fn) =>
      arr.map(typeof fn === 'function' ? fn : val => val[fn]).reduce((acc, val, i) => {
        acc[val] = (acc[val] || []).concat(arr[i]);
        return acc;
      }, {});

      //get all reservations for current day
      let todaysReservations = ''
      if (this.openHoursStart.format(this.momentDateFormat) === this.openHoursEnd.format(this.momentDateFormat)) {
        todaysReservations = this.reservations.filter(reservation => 
          ( 
            reservation.date.toLowerCase() === this.date.toLowerCase() && 
            moment(reservation.timeStart, this.momentTimeFormat).format(this.dbTimeFormatForMoment)  >= this.openHoursStart.format(this.dbTimeFormatForMoment)
          )
        )
      } else {
        todaysReservations = this.reservations.filter(reservation => {
          
          this.openHoursEnd.add(Number(this.calendarTimeInitData.reservation_duration), 'm') 

          let tmp = (
                      reservation.date.toLowerCase() === moment(this.date, this.momentDateFormat).add(1, 'day').format(this.momentDateFormat).toLowerCase() &&
                      moment(reservation.timeEnd, this.momentTimeFormat).format(this.dbTimeFormatForMoment) <= this.openHoursEnd.format(this.dbTimeFormatForMoment)
                    ) 
                    || 
                    ( 
                      reservation.date.toLowerCase() === this.date.toLowerCase() &&
                      moment(reservation.timeStart, this.momentTimeFormat).format(this.dbTimeFormatForMoment)  >= this.openHoursStart.format(this.dbTimeFormatForMoment)
                    )

          this.openHoursEnd.add(Number(-this.calendarTimeInitData.reservation_duration), 'm') 

          return tmp
        });
      }
      
      //prepare data for output
      let groupedReservationsForToday = groupBy(todaysReservations, 'timeStart')

      for (let time in groupedReservationsForToday) {
        this.summaryTable.push({
          'time': time,
          'tables': groupedReservationsForToday[time].sort((a, b) => a.table - b.table).map(v => v.table).toString().split(',').join(', ')
        })
      }


      const momentDate = moment(this.dateForClient, this.momentDateFormat)

      this.summaryTable.sort((a,b) => {

        let selectedTimeA = moment(a.time, this.momentTimeFormat).set({
          date: momentDate.date(),
          month: momentDate.month(),
          year: momentDate.year()
        })
        let selectedTimeB = moment(b.time, this.momentTimeFormat).set({
          date: momentDate.date(),
          month: momentDate.month(),
          year: momentDate.year()
        })

        if (moment(a.time, this.momentTimeFormat).format(this.dbTimeFormatForMoment) < this.openHoursStart.format(this.dbTimeFormatForMoment)) {
          selectedTimeA.add(1, 'd')
        }

        if (moment(b.time, this.momentTimeFormat).format(this.dbTimeFormatForMoment) < this.openHoursStart.format(this.dbTimeFormatForMoment)) {
          selectedTimeB.add(1, 'd')
        }

        return selectedTimeA.diff(selectedTimeB)

      })

     
    },


    selectedTableFreeModal() {
      if (this.table != '') {
        this.openModal('modalFree')
        this.tableModal = this.table
        this.isTableInDisabledTables = !(this.disabledTables.includes(this.tableModal.toString()))
      }
    },

    selectedTableReservedModal() {
      this.openModal('modalReserved')
    },

    openModal(ref) {
        this.$refs[ref].open();

        if (ref === 'modalFree') {
          this.timeSliderShow = true
          //for reset time after modal closed
          this.sliderValueBeforeModalOpened = this.sliderValue
        }
    },
    
    //for modalFree
    closeModal(ref) {
        this.$refs[ref].close();
        this.timeSliderShow = false
    },


    //for modalFree
    modalClosed() {
      //for reset time after modal closed
      this.sliderValue = this.sliderValueBeforeModalOpened 
    },

    initTimeEndSlider(newTimeStart) {
      this.sliderActive = true
      this.sliderMin =  Number(this.calendarTimeInitData.reservation_duration_min)

      //change sliderMax on the border
      let momentDate = moment(this.dateForClient, this.momentDateFormat)
      let timeStart = moment(newTimeStart, this.momentTimeFormat).set({
        date: momentDate.date(),
        month: momentDate.month(),
        year: momentDate.year()
      })
      const overtime = timeStart.diff(this.openHoursEnd, 'minutes') + Number(this.calendarTimeInitData.reservation_duration_max) - Number(this.calendarTimeInitData.reservation_duration)
      if (overtime > 0) {
        this.sliderMax =  Number(this.calendarTimeInitData.reservation_duration_max) - overtime
      } else {
        this.sliderMax =  Number(this.calendarTimeInitData.reservation_duration_max)
      }

      this.sliderTimeInterval = Number(this.calendarTimeInitData.slider_time_interval) 
      this.sliderTooltipFormat = '{value} ' + this.calendarTimeInitData.translation.minutes
      this.sliderValue = Math.floor((this.sliderMin + this.sliderMax)/2) - (Math.floor((this.sliderMin + this.sliderMax)/2))%this.sliderTimeInterval
      if (this.sliderValue < this.sliderMin) {
        this.sliderValue = this.sliderMin
      }
      if (this.sliderActive) {
        this.timeEnd = moment(this.timeStart, this.momentTimeFormat).add(Number(this.sliderValue), 'm').format(this.momentTimeFormat)
      } else {
        this.timeEnd = moment(this.timeStart, this.momentTimeFormat).add(Number(this.calendarTimeInitData.reservation_duration), 'm').format(this.momentTimeFormat)
      }
    },

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
          this.$refs.tremReservation.style.height = (this.$refs.reservation2envelope.clientHeight + 30).toString() + 'px'
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

        this.closeModal('modalFree')
        
        this.$http.post(this.calendarTimeInitData.url, {
          'action': 'tremtr_reservation',
          'nonce': this.calendarTimeInitData.nonce,
          'tremtr_reservation_date': date,
          'tremtr_reservation_time_begin': moment(this.timeStart, this.momentTimeFormat).format(this.dbTimeFormatForMoment),
          'tremtr_reservation_time_end': moment(this.timeEnd, this.momentTimeFormat).format(this.dbTimeFormatForMoment),
          'tremtr_reservation_table': this.tableModal,
          'tremtr_reservation_name': this.name,
          'tremtr_reservation_persons': this.persons,
          'tremtr_reservation_email': this.email,
          'tremtr_reservation_phone': this.phone,
          'tremtr_reservation_message': this.message,
          'tremtr_reservation_cafe': this.cafeName
        },
        {
          emulateJSON: true
        }).then(response => {

          if (response.bodyText.includes('"success":true')) {
            this.name = ''
            this.persons = ''
            this.email = ''
            this.phone = ''
            this.message = ''

            this.renewReservations()

            this.$toasted.show(this.calendarTimeInitData.translation.confirmedReservation, { 
              theme: "outline", 
              position: "bottom-right", 
              duration : 5000,
              className: 'toast',
              containerClass: 'toast-container-status'
            });
          } else {

            this.$toasted.show(this.calendarTimeInitData.translation.rejectRequest, { 
              theme: "primary", 
              position: "top-center", 
              duration : 7000,
              className: 'toast',
              containerClass: 'toast-container'
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

    deleteReservation() {
      this.closeModal('modalReserved')
      
      this.$http.delete(this.calendarTimeInitData.endpoint_reservation + '/' + this.modalReservedData.id, {
        headers: { 'X-WP-Nonce': this.calendarTimeInitData.nonce }
      }, {
        emulateJSON: true
      }).then(response => {
        
        
        if (response.statusText === 'OK') {
          this.modalReservedData = ''

          this.renewReservations()

          this.$toasted.show(this.calendarTimeInitData.translation.deletedReservation, { 
            theme: "outline", 
            position: "bottom-right", 
            duration : 5000,
            className: 'toast',
            containerClass: 'toast-container-status'
          });
        } else {

          this.$toasted.show(this.calendarTimeInitData.translation.rejectRequest, { 
            theme: "primary", 
            position: "top-center", 
            duration : 7000,
            className: 'toast',
            containerClass: 'toast-container'
          });
        }
        

      }, response => {
        // error callback
      });

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

      const fillActive = this.fillActive
      const fillActiveHover = this.fillActiveHover
      const fillBooked = this.fillBooked
      const fillBookedHover = this.fillBookedHover


      this.$http.get(this.calendarTimeInitData.endpoint_cafe).then(response => {

        // get body data 
        this.canvasInitData = response.body.tremtr_content
        this.cafeName = response.body.title.rendered

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

            if (h < 350) {

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

          for(let i = 0; i < this.canvas.getObjects().length; i=i+2){
            
            this.canvas.item(i).id = this.canvas.item(i + 1).text;
            this.canvas.item(i + 1).id = this.canvas.item(i + 1).text

            this.initCanvasObject (this.canvas.item(i))
            this.initCanvasObject (this.canvas.item(i + 1))
            
            this.canvas.item(i).set({
              fill: fillActive,
              strokeWidth : 0,
              opacity: 1
            });

            this.canvas.item(i+1).set({
              opacity: 1,
              left: this.canvas.item(i).left + 5,
              top: this.canvas.item(i).top + 5,
              fontSize: 14,
              fontWeight: 'bold'
            });

            this.canvas.renderAll()
          }

          for(let i = 0; i < this.canvas.getObjects().length; i+=2){
            this.tables.push({
              tableNumber: this.canvas.item(i).name, 
              canvasItem: i,
              state : 'free',
              timeStart: '',
              timeEnd: '',
              date: '',
              message: '',
              email: '',
              phone: '',
              persons: '',
              name: '',
              id: ''
            })
          }

          let called = false //for toasts

          this.canvas.on('mouse:over', (e) => {


            if (e.target != null) {
              if ((e.target.fill === fillActive) && (e.target.type === 'rect')) {
                e.target.set({
                  fill: fillActiveHover
                });
              }
              if ((e.target.fill === fillBooked) && (e.target.type === 'rect')) {
                e.target.set({
                  fill: fillBookedHover
                });
              }
              e.target.canvas.renderAll();
            }
          });


          this.canvas.on('mouse:out', (e) => {
            if (e.target != null) {
              if ((e.target.fill === fillActiveHover)  && (e.target.type === 'rect')) {
                if (!this.canvas.item(this.canvas.getObjects().indexOf(e.target)).active) {
                  e.target.set({
                    fill: fillActive
                  });
                }
              }
              if ((e.target.fill === fillBookedHover)  && (e.target.type === 'rect')) {
                if (!this.canvas.item(this.canvas.getObjects().indexOf(e.target)).active) {
                  e.target.set({
                    fill: fillBooked
                  });
                }
              }
              e.target.canvas.renderAll();
            }
          });


          
          this.canvas.observe('mouse:down', (e) => {

            if (((this.date === '') || (this.timeEnd === '') || (this.timeStart === '')) && !(called)) {

              this.$toasted.show(this.calendarTimeInitData.translation.canvasClickWarning, { 
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

            if (e.target && e.target.type === 'rect') {
              //for free table
              if (e.target.fill == fillActiveHover) {

                this.table = e.target.name;
                this.persons = e.target.persons;
                this.maxPersons = e.target.persons;

                this.selectedTableFreeModal()
              }

              //for reserved table
              if (e.target.fill == fillBookedHover) {
                
                this.tables.filter( table => {
                  if (table.tableNumber === e.target.name) {
                    this.modalReservedData = table
                  }
                })
                

                this.selectedTableReservedModal()
              }
            }
            
            this.canvas.renderAll()
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

        this.$http.get(this.calendarTimeInitData.endpoint_reservation, {
          headers: { 'X-WP-Nonce': this.calendarTimeInitData.nonce }
        }).then(response => {

          // get body data 
          this.reservations = response.body
          
          this.reservations = this.reservations.filter(reservation => reservation.cafe === this.cafeName)
          for (let reservation of this.reservations){
            reservation.date = moment(reservation.date, this.dbDateFormatForMoment).format(this.momentDateFormat)
            reservation.timeStart = moment(reservation.timeStart, this.dbTimeFormatForMoment).format(this.momentTimeFormat)
            reservation.timeEnd = moment(reservation.timeEnd, this.dbTimeFormatForMoment).format(this.momentTimeFormat)
          }
          //this.renewDisabledTables()
        }, response => {

          // error callback 
        });

      }, response => {
        // error callback 
      });

      
      
    },

    renewReservations() {
      
      this.$http.get(this.calendarTimeInitData.endpoint_reservation, {
        headers: { 'X-WP-Nonce': this.calendarTimeInitData.nonce }
      }).then(response => {

        // get body data 
        this.reservations = response.body
        this.reservations = this.reservations.filter(reservation => reservation.cafe === this.cafeName)
        for (let reservation of this.reservations){
          reservation.date = moment(reservation.date, this.dbDateFormatForMoment).format(this.momentDateFormat)
          reservation.timeStart = moment(reservation.timeStart, this.dbTimeFormatForMoment).format(this.momentTimeFormat)
          reservation.timeEnd = moment(reservation.timeEnd, this.dbTimeFormatForMoment).format(this.momentTimeFormat)
        }

        this.renewDisabledTables()

        if (this.date != ''){
          this.renewSummaryTable()
        }

      }, response => {


        // error callback 
      });
    },

    renewDisabledTables () {

      this.disabledTables = []

      this.tables.map( table => {
        table.date = '',
        table.persons = '',
        table.timeStart = '',
        table.timeEnd = '',
        table.name =  '',
        table.email =  '',
        table.message =  '',
        table.phone =  '',
        table.state = 'free',
        table.id = ''
      })

      if (this.timeStart !== '' && this.timeEnd != '') {
        
        let todaysReservations = ''

        if (this.openHoursStart.format(this.momentDateFormat) === this.openHoursEnd.format(this.momentDateFormat)) {
          todaysReservations = this.reservations.filter(reservation => 
            ( 
              reservation.date.toLowerCase() === this.date.toLowerCase() && 
              moment(reservation.timeStart, this.momentTimeFormat).format(this.dbTimeFormatForMoment)  >= this.openHoursStart.format(this.dbTimeFormatForMoment)
            )
          )
        } else {
          todaysReservations = this.reservations.filter(reservation => {
            
            this.openHoursEnd.add(Number(this.calendarTimeInitData.reservation_duration), 'm') 

            let tmp = (
                        reservation.date.toLowerCase() === moment(this.date, this.momentDateFormat).add(1, 'day').format(this.momentDateFormat).toLowerCase() &&
                        moment(reservation.timeEnd, this.momentTimeFormat).format(this.dbTimeFormatForMoment) <= this.openHoursEnd.format(this.dbTimeFormatForMoment)
                      ) 
                      || 
                      ( 
                        reservation.date.toLowerCase() === this.date.toLowerCase() &&
                        moment(reservation.timeStart, this.momentTimeFormat).format(this.dbTimeFormatForMoment)  >= this.openHoursStart.format(this.dbTimeFormatForMoment)
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
          let momentInitDateTimeBegin = moment(todaysReservation.date, this.momentDateFormat)
          let timeBegin = moment(todaysReservation.timeStart, this.momentTimeFormat).set({
            date: momentInitDateTimeBegin.date(),
            month: momentInitDateTimeBegin.month(),
            year: momentInitDateTimeBegin.year()
          })
          let timeEnd = moment(todaysReservation.timeEnd, this.momentTimeFormat).set({
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
            this.disabledTables.push(todaysReservation.table)

            this.changeItemInTablesArray(todaysReservation)


          }
        }
      }
      this.disableTable()
    },

    changeItemInTablesArray(todaysReservation) {
      
      this.tables.filter( table => {
        if (table.tableNumber === Number(todaysReservation.table)) {
          const { 
            date, 
            persons, 
            timeStart, 
            timeEnd, 
            name,
            email,
            message,
            phone,
            id
          } = todaysReservation

          table.date = date,
          table.persons = persons,
          table.timeStart = timeStart,
          table.timeEnd = timeEnd,
          table.name =  name,
          table.email =  email,
          table.message =  message,
          table.phone =  phone,
          table.state = 'reserved',
          table.id = id
        } 
      })
    },

    disableTable () {
      
      for(let i = 0; i < this.canvas.getObjects().length; i= i+2){

        if (this.disabledTables.includes(this.canvas.item(i).name.toString())) {
          this.canvas.item(i).set({
            fill: this.fillBooked,
            evented: true
          });

          //add text with information about reservation
          this.tables.filter( table => {
            if (Number(this.canvas.item(i).name) === table.tableNumber) {

              if (this.canvas.item(i+1).text.indexOf('  ') !== -1) {
                this.canvas.item(i+1).set({
                  text: this.canvas.item(i+1).text.substr(0, this.canvas.item(i+1).text.indexOf('  ')) + '  ' + table.persons + '👥' + '\n' + table.timeStart + '-' + '\n' + table.timeEnd
                })
              } else {
                this.canvas.item(i+1).set({
                  text: this.canvas.item(i+1).text + '  ' + table.persons + '👥' + '\n' + table.timeStart + '-' + '\n' + table.timeEnd
                })
              }
            }
          })
          

          if (this.canvas.item(i).name.toString() === this.table.toString()) {
            this.canvas.item(i).set({
              fill: this.fillBooked,
              evented: true,
              active: false
            });

            this.table = ''
          }
        }

        if (!(this.disabledTables.includes(this.canvas.item(i).name.toString()))) {
          if (this.canvas.item(i).fill === this.fillBooked || this.canvas.item(i).fill === this.fillBookedHover) {
            this.canvas.item(i).set({
              fill: this.fillActive,
              evented: true
            });
          }
          //remove text with information about reservation
          if (this.canvas.item(i+1).text.indexOf(' ') !== -1) {
            this.canvas.item(i+1).set({
              text: this.canvas.item(i+1).text.substr(0, this.canvas.item(i+1).text.indexOf(' '))
            });
          }
        }

        
      }
      this.canvas.renderAll()
    },

    makeTablesSelectable () {

        if (this.canvas !== ''){
            if ((this.date !== '') && (this.timeEnd !== '') && (this.timeStart !== '')) {
              for(let i = 0; i < this.canvas.getObjects().length; i= i+2){

                if (!(this.disabledTables.includes(this.canvas.item(i).name.toString()))) { 

                  this.canvas.item(i).set({
                    evented: true
                  });
                }

              }
              this.canvas.renderAll()
            } else {
              for(let i = 0; i < this.canvas.getObjects().length; i= i+2){

                this.canvas.item(i).set({
                  evented: false,
                  active: false,
                  fill: this.fillActive
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

