import { IsString, IsOptional, IsNumber, IsDateString, IsUUID, IsInt } from 'class-validator';

export class CreateHotelDto {
  @IsUUID()
  tripId: string;

  @IsString()
  hotelName: string;

  @IsString()
  city: string;

  @IsOptional()
  @IsString()
  country?: string;

  @IsDateString()
  checkIn: string;

  @IsDateString()
  checkOut: string;

  @IsString()
  roomType: string;

  @IsOptional()
  @IsString()
  boardBasis?: string;

  @IsOptional()
  @IsInt()
  numberOfRooms?: number;

  @IsInt()
  numberOfGuests: number;

  @IsOptional()
  @IsString()
  confirmationRef?: string;

  @IsOptional()
  @IsString()
  status?: string;

  @IsOptional()
  @IsNumber()
  costPrice?: number;

  @IsOptional()
  @IsNumber()
  sellPrice?: number;

  @IsOptional()
  @IsUUID()
  supplierId?: string;

  @IsOptional()
  @IsString()
  supplierRef?: string;

  @IsOptional()
  @IsString()
  notes?: string;
}

export class UpdateHotelDto {
  @IsOptional() @IsString() hotelName?: string;
  @IsOptional() @IsString() city?: string;
  @IsOptional() @IsString() country?: string;
  @IsOptional() @IsDateString() checkIn?: string;
  @IsOptional() @IsDateString() checkOut?: string;
  @IsOptional() @IsString() roomType?: string;
  @IsOptional() @IsString() boardBasis?: string;
  @IsOptional() @IsInt() numberOfRooms?: number;
  @IsOptional() @IsInt() numberOfGuests?: number;
  @IsOptional() @IsString() confirmationRef?: string;
  @IsOptional() @IsString() status?: string;
  @IsOptional() @IsNumber() costPrice?: number;
  @IsOptional() @IsNumber() sellPrice?: number;
  @IsOptional() @IsUUID() supplierId?: string;
  @IsOptional() @IsString() supplierRef?: string;
  @IsOptional() @IsString() notes?: string;
}
