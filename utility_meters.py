#!/usr/bin/env python3.4
import pymysql
import time
import datetime

# object based on meter id
class UtilityMeter:
    def __init__(self, mId, mType, mEpoch, mConsumption, dbCur):
        self.mId            = mId
        self.mType          = mType
        self.mEpoch         = mEpoch
        self.mConsumption   = mConsumption
        #
        self.time1          = mEpoch
        self.consumption1   = mConsumption
        self.time2          = mEpoch
        self.consumption2   = mConsumption
        #
        self.dbCur          = dbCur

class ElectricMeter(UtilityMeter):
    def getCurrentWatts(self, currTime, currConsumption):
        #time
        self.time = datetime.datetime.fromtimestamp(time.time()).strftime('%Y-%m-%d %H:%M:%S')
        self.watts = 0
        self.time2   = currTime
        self.consumption2  = currConsumption

        self.timeDiff = self.time2 - self.time1
        if(self.timeDiff < 0):
            print("Error: Time Diff Negative. Customer: %s. %d - %d = %d" % (self.mId, self.time2, self.time1, self.timeDiff))
        # min 5min granularity
        if(self.timeDiff >= 300):
            # figure out the power used in this time
            self.powerDiff = self.consumption2 - self.consumption1
            # if the power hasn't incremented then do nothing
            if(self.powerDiff != 0):
                # reset time1 and consumption1
                self.time1 = currTime
                self.consumption1 = currConsumption

                # convert power diff from kwh to kws
                self.watts = (self.powerDiff * 3600 /self.timeDiff)
                # if numbers are way out of range throw error
                if(self.watts > 10000 or self.watts < -10000):
                    print("Calculated use out of range! Got:")
                    print("[%s] Customer %s Using %f watts. %d Wh / %d s" % (self.time, self.mId, self.watts, self.powerDiff, self.timeDiff))
                    return -1
                print("[%s] Customer %s Using %f watts. %d Wh / %d s" % (self.time, self.mId, self.watts, self.powerDiff, self.timeDiff))

                # write to db
                self.dbCur.execute("insert into UtilityMeter(mId, mType, mTime, mTotalConsumption, mConsumed) values (%s, %d, %d, %d, %f)" % (self.mId, int(self.mType), int(currTime), int(currConsumption), self.watts))

        return self.watts

class GasMeter(UtilityMeter):
    # cubic feet / sec ??
    def getGasPerSec(self, currTime, currConsumption):
        #time
        self.time = datetime.datetime.fromtimestamp(time.time()).strftime('%Y-%m-%d %H:%M:%S')
        self.gasPerSec = 0
        self.time2  = currTime
        self.consumption2   = currConsumption

        self.timeDiff = self.time2 - self.time1
        if(self.timeDiff < 0):
            print("Error: Time Diff Negative. Customer: %s. %d - %d = %d" % (self.mId, self.time2, self.time1, self.timeDiff))
        # min 5min granularity
        if(self.timeDiff >= 300):
            # calculate gas / sec
            self.gasDiff = self.consumption2 - self.consumption1
            # if it hasn't changed do nothing
            if(self.gasDiff != 0):
                # reset time1 and consumption1
                self.time1 = currTime
                self.consumption1 = currConsumption

                self.gasPerSec = self.gasDiff / self.timeDiff
                # if numbers are way out of range throw error
                if(self.gasPerSec > 10000 or self.gasPerSec < -10000):
                    print("Calculated use out of range! Got:")
                    print("[%s] Customer %s Using %f cubic feet / sec. %d / %d s" % (self.time, self.mId, self.gasPerSec, self.gasDiff, self.timeDiff))
                    return -1
                print("[%s] Customer %s Using %f cubic feet / sec. %d / %d s" % (self.time, self.mId, self.gasPerSec, self.gasDiff, self.timeDiff))

                # write to db
                self.dbCur.execute("insert into UtilityMeter(mId, mType, mTime, mTotalConsumption, mConsumed) values (%s, %d, %d, %d, %f)" % (int(self.mId), int(self.mType), int(currTime), int(currConsumption), self.gasPerSec))

        return self.gasPerSec

class WaterMeter(UtilityMeter):
    # cubic feet / sec ??
    def getWaterPerSec(self, currTime, currConsumption):
        #time
        self.time = datetime.datetime.fromtimestamp(time.time()).strftime('%Y-%m-%d %H:%M:%S')
        self.waterPerSec = 0
        self.time2  = currTime
        self.consumption2   = currConsumption

        self.timeDiff = self.time2 - self.time1
        if(self.timeDiff < 0):
            print("Error: Time Diff Negative. Customer: %s. %d - %d = %d" % (self.mId, self.time2, self.time1, self.timeDiff))
        # min 5min granularity
        if(self.timeDiff >= 300):
            # calculate water / sec
            self.waterDiff = self.consumption2 - self.consumption1
            # if it hasn't changed do nothing
            if(self.waterDiff != 0):
                # reset time1 and consumption1
                self.time1 = currTime
                self.consumption1 = currConsumption

                self.waterPerSec = self.waterDiff / self.timeDiff
                # if numbers are way out of range throw error
                if(self.waterPerSec > 10000 or self.waterPerSec < -10000):
                    print("Calculated use out of range! Got:")
                    print("[%s] Customer %s Using %f cubic feet / sec. %d / %d s" % (self.time, self.mId, self.waterPerSec, self.waterDiff, self.timeDiff))
                    return -1
                print("[%s] Customer %s Using %f cubic feet / sec. %d / %d s" % (self.time, self.mId, self.waterPerSec, self.waterDiff, self.timeDiff))

                # write to db
                self.dbCur.execute("insert into UtilityMeter(mId, mType, mTime, mTotalConsumption, mConsumed) values (%s, %d, %d, %d, %f)" % (int(self.mId), int(self.mType), int(currTime), int(currConsumption), self.waterPerSec))

        return self.waterPerSec
